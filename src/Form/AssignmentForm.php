<?php

namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Cake\View\Form\FormContext;
use Cake\Collection\Collection;
use App\Lib\Range;
use App\Lib\EditionTypeMap;

/**
 * @todo Has the PieceAssignmentComponent been abandoned as a concept?
 * PieceAssignmentComponent was stubbed to centralize assingment rules. But
 * this single form currently takes care of it all. Is this ok? Should the
 * Component become a Lib class?
 * Superficially, since only the assign() method allows these kinds of edits,
 * and this Form class oversees all the associated logic, this may be the
 * correct place to localize the logic. But if later streamlined piece movement
 * is allowed, like through an ajax process, a Lib class may be the right choice.
 *
 * @todo Consider custom numbering schemes (see notes in App\Lib\Range)
 *
 * @todo Lib\Range planning is begining to touch on some validation issues
 * The ideas of expanded range-syntax support to include new numbering schemes
 * and reference to sets of Editions or use of Series will effect this Form.
 * Also, the idea of having a separate class the validates a properly expressed
 * range against real-world data is breathing new life into the idea of the
 * PieceAssignment component or Lib class.
 * If a class that queries for data based on the range string is also created
 * it would form the basis of an ajax based piece filtering tool for the UX.
 * This starts to implicate App\Lib\PieceFilter in the set of involved classes.
 */
class AssignmentForm extends Form {

    public $_providers;
    protected $_form_data;
    public $source_pieces = [];
    public $source_quantity = 0;
    public $request_quantity = 0;
    public $source_numbers = [];
    public $request_numbers = [];
    public $destination = '';
    public $destination_piece = [];

    public function __construct($providers) {
        $this->_providers = $providers;
        parent::__construct();
    }

    protected function _buildSchema(Schema $schema) {
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

    protected function _buildValidator(Validator $validator) {

//        osd($validator);
//		osd('this string ' + 'that string');
        $validator
            ->allowEmptyString('to_move', FALSE, 'You must indicate which pieces should be moved.')
            ->allowEmptyString('destinations_for_pieces', FALSE, 'You must choose a destination for the pieces.')
            ->add('destinations_for_pieces', [
                'same_as_source' => [
                    'rule' => [$this, 'filterSource'],
                    'message' => 'There was only one source chosen and it was the '
                    . 'same as the destination. No action was taken.'
                ]
        ]);
//        osd($validator);die;

        if (EditionTypeMap::isUnNumbered($this->_providers->edition->type)) {
            // open/unNumbered editions allow an integer value
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
                    // This rule attempts to change the callable location
                    // untested as of 11/2018
                    'rule' => [Range::patternValidation],
//                  'rule' => [$this, 'rangePatternValidation'],
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
        return (boolean) iterator_count($sources);
    }

    /**
     * Insure the range describing numbered pieces to move is valid
     *
     * @todo Shouldn't App\Lib\Range do this? Yes, the method has been
     * 		created as a static function on that class but the one use is
     * 		as a callable ('rule' => [$this, 'rangePatternValidation'])
     * 		earlier in this class so I'll have to be make sure that I can
     * 		use the static function as a callable.
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
     * Insure there is a source that isn't the destination
     *
     * @param mixed $value
     * @param array $context
     * @return boolean
     */
    public function filterSource($value, $context) {
        $sources = array_flip($this->_chosenSources($context)->toArray());
        unset($sources[0]);
        unset($sources[$value]);
        return !empty($sources);
    }

    /**
     * Insure there are enough pieces to move
     *
     * @param mixed $value
     * @param array $context
     * @return boolean
     */
    public function piecesAvailabilityConfirmation($context) {
//        osd($this->_providers);die;
        $context = $this->_removeMatchingSource($context);
        if (EditionTypeMap::isUnNumbered($this->_providers->edition->type)) {
            return $this->checkOpenAvailability($context);
        } else {
            return $this->checkNumberedAvailability($context);
        }
    }

    /**
     * Make sure the source that matches the destination is not included
     *
     * @param array $context
     */
    protected function _removeMatchingSource($context) {
        $columns = $context['data'];
        $keys = array_keys($columns);
        $count = 0;
        // source columns are at the start of the list so we can skip out quickly
        while (stristr($keys[$count], 'source_for_pieces_') &&
        $columns[$keys[$count]] !== $columns['destinations_for_pieces']) {
            $count++;
        }
        if (stristr($keys[$count], 'source_for_pieces_')) {
            $context['data'][$keys[$count]] = '0';
        }

        return $context;
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
    protected function checkNumberedAvailability($context) {
        $this->_sourcePieces($context);

        $this->source_numbers = (new Collection($this->source_pieces))->combine('{n}', 'number');
        $this->request_numbers = Range::stringToArray($context['data']['to_move']);
        $bad_request = array_diff($this->request_numbers, $this->source_numbers->toArray());
        if (!empty($bad_request)) {
            $result = FALSE;
            $grammar = count($bad_request) > 1 ? 'are' : 'is';
            $bad_range = Range::arrayToString($bad_request);

            $good_request = array_intersect($this->source_numbers->toArray(), $this->request_numbers);
            $good_range = Range::arrayToString($good_request);

            if ($good_range) {
                $try_this = '.<br />The available pieces in your request: ' . $good_range;
            } else {
                $try_this = " or $grammar already in the destination.";
            }

            $this->_errors['to_move'] = [
                'missing_pieces' => "$bad_range $grammar not available in the selected sources$try_this"];
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
                $this->_providers->providers[$provider_key]->assignablePieces(PIECE_ENTITY_RETURN)
            );
        });
        return $this->source_pieces;
    }

    protected function _identifyOpenDestinationPiece($data) {
        if (stristr($this->destination, 'Edition')) {
            $this->destination_piece = $this->_providers['edition']->unassigned;
        } else {
            $fragments = explode('\\', $data['destinations_for_pieces']);
            $format_id = array_pop($fragments);
            $class_name = implode('\\', $fragments);

            $destination_format = (new Collection($this->_providers))->filter(
                    function($value) use($format_id, $class_name) {
                    return get_class($value) == $class_name && $value->id == $format_id;
                })->toArray();

            $destination_format = array_pop($destination_format);
            $this->destination_piece = $destination_format->fluid;
        }
    }

    protected function _execute(array $data) {
//        osd($data);die;
        // data is packaged to match Validator::context because we reuse many of its callbacks
        $result = $this->piecesAvailabilityConfirmation(['data' => $data]);

        if ($result) {
            // the final validaton passed so set up some final values for save
            $this->destination = $data['destinations_for_pieces'];

            if (EditionTypeMap::isUnNumbered($this->_providers->edition->type)) {
                $this->_identifyOpenDestinationPiece($data);
                $this->destination_piece = array_pop($this->destination_piece);
            }
        }

        return $result;
    }

}
