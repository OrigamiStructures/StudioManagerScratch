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
		
		if (in_array($this->_providers['edition']->type, SystemState::openEditionTypes())) {
			$validator->add('to_move', [
				'open_move' => [
					'rule' => 'isInteger',
					'message' => 'Open editions require a number of pieces to move']]);
			
		} else {
			// regex to verify a proper range value
		}
		
		return $validator;
    }

    protected function _execute(array $data)
    {
        // Send an email.
        return true;
    }
}