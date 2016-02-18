<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * CakePHP DispositionToolsHelper
 * @author dondrake
 */
class DispositionToolsHelper extends Helper {
	
	public $helpers = ['Html'];
	
	protected $dispo_label;
	
	public function connect($entity) {
		$class = array_pop((explode('\\', get_class($entity))));
		$method = "_connect$class";
		
		return $this->$method($entity);
	}
	
	protected function _connectMember($member) {
		$label = $this->_label($member->name);
		return $this->Html->link($label, [
			'controller' => 'dispositions',
            'action' => 'create', 
            '?' => [
                'member' => $member->id,
                ]
            ]//, 
//            ['class' => 'button']
		);
	}
	
	protected function _connectAddress($address) {
		$label = $this->_label($address->address1);
		return $this->Html->link($label, [
			'controller' => 'dispositions',
            'action' => 'create', 
            '?' => [
                'address' => $address->id,
                ]
            ]//, 
//            ['class' => 'button']
		);
	}
	
	private function _label($name) {
		if (!isset($this->dispo_label)) {
			$this->dispo_label = $this->_View->viewVars['standing_disposition']->label;
		}
		switch ($this->dispo_label) {
			case DISPOSITION_LOAN_CONSIGNMENT :
			case DISPOSITION_LOAN_PRIVATE :
			case DISPOSITION_LOAN_RENTAL :
			case DISPOSITION_TRANSFER_DONATION :
			case DISPOSITION_TRANSFER_SALE :
			case DISPOSITION_TRANSFER_GIFT :
				$label = "$this->dispo_label to $name";
				break;
			case DISPOSITION_LOAN_SHOW :
			case DISPOSITION_STORE_STORAGE :
				$label = "$this->dispo_label at $name";
				break;
		}
		return $label;
		/**
		define('DISPOSITION_UNAVAILABLE_LOST'	, 'Lost');
		define('DISPOSITION_UNAVAILABLE_DAMAGED', 'Damaged');
		define('DISPOSITION_UNAVAILABLE_STOLEN' , 'Stolen');
		 */
	}
	
}
