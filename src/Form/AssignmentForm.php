<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use App\Lib\SystemState;
use Cake\View\Form\FormContext;
use Cake\Collection\Collection;

class AssignmentForm extends Form
{
	protected $_providers;
	protected $_form_data;
	public $source_pieces = [];
	public $source_quantity = 0;
	public $request_quantity =0;
	public $source_numbers = [];
	public $request_numbers = [];
	public $destination = '';
	
	public function __construct($providers) {
		$this->_providers = $providers;
	}

    protected function _buildSchema(Schema $schema)
    {
		//[
		//	'source_for_pieces_0' => 'App\Model\Entity\Edition\6',
		//	'source_for_pieces_1' => 'App\Model\Entity\Format\6',
		//	'to_move' => 'xx',
		//	'destinations_for_pieces' => 'App\Model\Entity\Format\8'
		//]
        $schema->addField('destinations_for_pieces', ['type' => 'string'])
			->addField('to_move', ['type' => 'datetime']);
		
		$count = 0;
		foreach ($this->_providers as $provider) {
			$schema->addField("source_for_pieces_$count", ['type' => 'string']);
			$count++;
		}
            
		return $schema;
    }

    protected function _buildValidator(Validator $validator)
    {
//		osd('this string ' + 'that string');
        $validator
			->notEmpty('to_move', 'You must indicate which pieces should be moved.')
			->notEmpty('destinations_for_pieces', 'You must choose a destination for the pieces.');
		
		if (SystemState::isOpenEdition($this->_providers['edition']->type)) {
			// open editions allow an integer value
			$validator
				->add('to_move', [
					'open_move' => [
						'rule' => 'isInteger',
						'message' => 'Open editions require a number of pieces to move']
				])
				->add('source_for_pieces_0', 'available_source', [
					'rule' => [$this, 'sourceValidation'],
					'message' => 'You must indicate at least one source for pieces',
//				])
//				->add('to_move', 'available_pieces', [
//					'rule' => [$this, 'piecesAvailableValidation'],
//					'message' => 'You asked to move more pieces than were available in the indicated source(s)',
				]);
//			osd($this->_providers['KEY'],'CHOSEN SOURCES PROPERTY');
			
		} else {
			// limited editions allow range values
			$validator
				->add('to_move', 'limit_move', [
					'rule' => [$this, 'rangePatternValidation'],
					'message' => "Use numbers (eg. 27) or ranges (eg. 3-7) separated by commas (, )."
					. "<br /> 5-7, 9, 12-13 would return 5, 6, 7, 9, 12, 13 ",
//				])
//				->add('to_move', 'available_pieces', [
//					'rule' => [$this, 'piecesAvailableValidation'],
//					'message' => 'Some of the pices you asked to move were not free or did not exist.',
				]);

		}
		
		return $validator;
    }
	
	/**
	 * Insure at there is at least one source to draw from
	 * 
	 * @param mixed $value
	 * @param array $context
	 * @return boolean
	 */
	public function sourceValidation($value, $context) {
		$sources = $this->_chosenSources($context);
		return (boolean) iterator_count($sources) ;
	}
	
	/**
	 * Insure the range describing numbered pieces to move is valid
	 * 
	 * @param mixed $value
	 * @param array $context
	 * @return boolean
	 */
	public function rangePatternValidation($value, $context) {
		$pattern = '/(\d+-\d+|\d+)(, *(\d+-\d+|\d+))*/'; 
		preg_match($pattern, $value, $match);

		return $value === $match[0];
	}
	
	/**
	 * Insure there are enough pieces to move
	 * 
	 * @param mixed $value
	 * @param array $context
	 * @return boolean
	 */
	public function piecesAvailabilityConfirmation ($context) {
		if (SystemState::isOpenEdition($this->_providers['edition']->type)) {
			return $this->checkOpenAvailability($context);
		} else {
			osd('numbered path');
			return $this->checkNumberedAvailability($context);
		}
	}
	
	/**
	 * Insure there are enough Open Edition pieces to move in the selected sources
	 * 
	 * @param mixed $value
	 * @param array $context
	 * @return boolean
	 */
	protected function checkOpenAvailability($context) {
		$this->_sourcePieces($context);
		$this->source_quantity = (new Collection($this->source_pieces))->sumOf(function ($piece) {
				return $piece->quantity;
			});

		if ($this->source_quantity >= $context['data']['to_move']) {
			$result = TRUE;
			$this->request_quantity = $context['data']['to_move'];
		} else {
			$result = FALSE;
			$difference = $context['data']['to_move'] - $this->source_quantity;
			// set flash message here
			$this->_errors['to_move'] = ['piece_quantity' => "There are $this->source_quantity pieces in the selected sources "
					. "and you have asked to move {$context['data']['to_move']}. "
					. "Reduce your request by at least $difference pieces."];
		}
		return $result;
	}
	

	/**
	 * Insure there are enough numbered Edition pieces to move in the selected sources
	 * 
	 * @param mixed $value
	 * @param array $context
	 * @return boolean
	 */
		protected function checkNumberedAvailability($context){
		$this->_sourcePieces($context);
		$this->source_numbers = (new Collection($this->source_pieces))->combine('{n}', 'number');
		$this->request_numbers = \App\Lib\Range::parseRange($context['data']['to_move']);
		$bad_request = array_diff($this->request_numbers, $this->source_numbers->toArray());
		if (!empty($bad_request)) {
			$result = FALSE;
			$grammar = count($bad_request) > 1 ? 'are' : 'is';
			$bad_range = \App\Lib\Range::constructRange($bad_request);
			
			$good_request = array_intersect($this->source_numbers->toArray(), $this->request_numbers);
			$good_range = \App\Lib\Range::constructRange($good_request);
			
			if ($good_range) {
				$try_this = '<br />The available pieces in your request: ' . $good_range;
			} else {
				$try_this = '';
			}
			
			$this->_errors['to_move'] = [
				'missing_pieces' =>  "$bad_range $grammar not available in the selected sources$try_this"];
		} else {
			$result = TRUE;
		}
		return $result;
//		osd($this->_providers);
//		return $sources;
	}
	
	/**
	 * Return the trd nodes that are the chosen sources for pieces
	 * 
	 * @param array $context
	 * @return Collection
	 */
	protected function _chosenSources($context) {
		return (new Collection($context['data']))
			->filter(function($value, $key) {
				return (stristr($key, 'source_for_pieces_')) && !empty($value);
			});
	}

	/**
	 * Get all the reassignable pieces from the select sources
	 * 
	 * @param array $context
	 */
	protected function _sourcePieces($context) {
		$sources = $this->_chosenSources($context);
		$sources->each(function($value, $key) {
			$index = intval(str_replace('source_for_pieces_', '', $key));
			$provider_key = $index === 0 ? 'edition' : $index - 1;
			$this->source_pieces = array_merge(
				$this->source_pieces, 
				$this->_providers[$provider_key]->assignablePieces(PIECE_ENTITY_RETURN)
			);
		});

		return $this->source_pieces;
	}

	protected function _execute(array $data)
    {
		// data is packaged to match Validarot::context because we reuse many of its callbacks
		$result = $this->piecesAvailabilityConfirmation(['data' => $data]);
		$this->destination = $data['destinations_for_pieces'];

		return $result;
    }
}