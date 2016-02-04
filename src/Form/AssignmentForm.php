<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use App\Lib\SystemState;
use Cake\View\Form\FormContext;

class AssignmentForm extends Form
{
	protected $_providers;
	protected $_chosen_sources;

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
		
		if (SystemState::isOpenEditionTypes($this->_providers['edition']->type)) {
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
				])
				->add('to_move', 'available_pieces', [
					'rule' => [$this, 'piecesAvailableValidation'],
					'message' => 'You asked to move more pieces than were available in the indicated source(s)',
				]);
			
		} else {
			// limited editions allow range values
			$validator
				->add('to_move', 'limit_move', [
					'rule' => [$this, 'rangePatternValidation'],
					'message' => "Use numbers (eg. 27) or ranges (eg. 3-7) separated by commas (, )."
					. "<br /> 5-7, 9, 12-13 would return 5, 6, 7, 9, 12, 13 ",
				])
				->add('to_move', 'available_pieces', [
					'rule' => [$this, 'piecesAvailableValidation'],
					'message' => 'Some of the pices you asked to move were not free or did not exist.',
				]);

		}
		
		return $validator;
    }
	
	public function sourceValidation($value, $context) {
		$sources = (new \Cake\Collection\Collection($context['data']))
			->filter(function($value, $key) {
				return (stristr($key, 'source_for_pieces_')) && !empty($value);
			});
		return (boolean) iterator_count($sources) ;
	}
	
	public function rangePatternValidation($value, $context) {
		$pattern = '/(\d+-\d+|\d+)(, *(\d+-\d+|\d+))*/'; 
		preg_match($pattern, $value, $match);

		return $value === $match[0];
	}
	
	public function piecesAvailableValidation ($value, $context) {
		if (SystemState::isOpenEditionTypes($this->_providers['edition']->type)) {
			return $this->checkOpenAvailability($value, $context);
		} else {
			return $this->checkNumberedAvailability($value, $context);
		}
	}
	
	protected function checkOpenAvailability($value, $context) {
		return true;
	}
	
	protected function checkNumberedAvailability($value, $context){
		return true;
	}

	protected function _execute(array $data)
    {
        // Send an email.
        return true;
    }
}