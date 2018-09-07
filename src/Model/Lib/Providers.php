<?php
namespace App\Model\Lib;

use \App\Exception\BadEditionStackContentException;

/**
 * Providers
 * 
 * Either an Edition or its Formats can provide pieces. When operating on 
 * a Piece it is often necessary to be able to access its owner or infomration 
 * about that owner.
 * 
 * Making this Provider object encapsulates the logic needed to accomplish 
 * these ancestor-focused tasks. It eliminates the data-heavy alternative of 
 * placing an owner entity inside each Piece as a property.
 *
 * @author dondrake
 */
class Providers {
	
	/**
	 * So code can loop over all the provider entities
	 * 
	 * Accessible as Providers->providers
	 *
	 * @var array
	 */
	protected $_providers;
	
	/**
	 * Quick access to the Edtion entity
	 * 
	 * Accessible as Providers->edition
	 *
	 * @var Edition 
	 */
	protected $_edition = FALSE;
	
	/**
	 * So code can loop over just the Format entities
	 *
	 * Accessible as Providers->formats
	 * 
	 * @var array 
	 */
	protected $_formats = [];
	
	/**
	 * Hash table for looking up the title of Piece owners
	 * 
	 * Keys are generated by ParentEntityTrait::_key() in cooperation with 
	 * each entities key() method. 
	 * 
	 * @var array
	 */
	protected $_provider_titles = [];

	/**
	 * Validate and store the provided values
	 * 
	 * Provider requires an Edition Entity and all of its Format Entities
	 * 
	 * @param array $providers Edition and all its descendant Formats
	 * @throws BadEditionStackContentException
	 */
	public function __construct(array $providers) {
		foreach ($providers as $entity) {
			if (get_class($entity) === 'App\Model\Entity\Format') {
				$this->formats[] = $entity;
			} elseif (get_class($entity) === 'App\Model\Entity\Edition') {
				$this->edition = $entity;
			}
			$this->_provider_titles[$entity->key()] = $entity->display_title;
		}
		if (!$this->edition ||
				(count($this->formats) != $this->edition->format_count) ||
				!$this->_allRelated()) {
			throw new BadEditionStackContentException('Provider requires one Edition and all of its Formats');
		}
		$this->_providers = ['edition' => $this->edition] + $this->formats;
		
	}
	
	/**
	 * Return the protected properties without the underscored name
	 * 
	 * @param string $name Name of the protected property
	 * @return mixed
	 */
	public function __get($name) {
		if (array_key_exists($name, ['providers', 'edition', 'formats'])) {
			return $this->{"_$name"};
		}
	}
	
	/**
	 * Verify that all the formats belong to the edition
	 * 
	 * @return boolean
	 */
	private function _allRelated() {
		for ($i = 0; $i < $this->edition->format_count; $i++) {
			if ($this->format[$i]->edition_id !== $this->edition->id) {
				return FALSE;
			}
		}
		return TRUE;
	}
	
	/**
	 * Return the title of the Entity owning a piece
	 * 
	 * Every Piece has a key() method that uniquely 
	 * describes its owner. This provides the basis for owner-title access.
	 * 
	 * @param string $key
	 * @return string
	 * @throws BadEditionStackContentException
	 */
	public function title($key) {
		if (isset($this->_provider_titles[$key])) {
			return $this->_provider_titles[$key];
		} else {
			throw new BadEditionStackContentException("The key $key is not in "
					. "the current Provider family. No title could be returned.");
		}
		
	}
}
