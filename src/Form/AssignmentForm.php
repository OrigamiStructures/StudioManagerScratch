<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

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
        return $validator->add('destinations_for_pieces', 'length', [
                'rule' => ['minLength', 1],
                'message' => 'A destination is required'
////            ])->add('email', 'format', [
////                'rule' => 'email',
////                'message' => 'A valid email address is required',
            ]);
    }

    protected function _execute(array $data)
    {
        // Send an email.
        return true;
    }
}