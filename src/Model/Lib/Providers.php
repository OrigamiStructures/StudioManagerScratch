<?php

namespace App\Model\Lib;

use \App\Exception\BadEditionStackContentException;
use Cake\Utility\Text;
use App\Lib\EditionTypeMap;

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
            if (get_class($entity) === 'App\Model\Entity\EditionsFormat') {
                $this->_formats[] = $entity;
            } elseif (get_class($entity) === 'App\Model\Entity\Edition') {
                $this->_edition = $entity;
            }
            $this->_provider_titles[$entity->key()] = $entity->display_title;
        }
        $this->_providers = ['edition' => $this->_edition] + $this->_formats;

        // Exception and detailed message
        if (!$this->_edition ||
            count($this->_formats) != $this->_edition->format_count ||
            !$this->_allRelated()) {
            $message = !$this->_edition ? " Edition missing. " : ' ';
            $message .= (count($this->_formats) != $this->_edition->format_count) ?
                (count($this->_formats) . ' formats but ' .
                $this->_edition->format_count . ' required. ') :
                '';
            $message .= !$this->_allRelated() ? 'At least one Format is not a '
                . 'decendant of the Edition.' : '';
            throw new BadEditionStackContentException('Provider requires one Edition and '
                . 'all of its Formats. ' . $message);
        }
    }

    /**
     * Return the protected properties without the underscored name
     *
     * @param string $name Name of the protected property
     * @return mixed
     */
    public function __get($name) {
        if (in_array($name, ['providers', 'edition', 'formats'])) {
            return $this->{"_$name"};
        }
    }

    /**
     * Verify that all the formats belong to the edition
     *
     * @return boolean
     */
    private function _allRelated() {
        for ($i = 0; $i < $this->_edition->format_count; $i++) {
            if ($this->_formats[$i]->edition_id !== $this->_edition->id) {
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

            // Exception and detailed message
            $message = 'Valid keys are: ' . Text::toList(array_keys($this->_provider_titles));
            throw new BadEditionStackContentException("The key $key is not in "
                . "the current Provider family. No title could be returned. $message");
        }
    }

    /**
     * Is this family one of the Limited Edition types?
     *
     * @todo This concept is changing to numbered editions. The current
     * 		thinking is that open editions may also be numbered in the
     * 		future. This may mean changing the concept of Limited to Numbered
     * 		and letting the artist control the limits. Then Open would
     * 		become UnNumbered. This just amounts to terminology changes
     * 		but if done, this code should get fixed too.
     * @todo This is a redundant method. Is there a reason for it to exist?
     *
     * @return boolean
     */
    public function isLimitedEdition() {
        return EditionTypeMap::isNumbered($this->_edition->type);
    }

    /**
     * Simplify debug output
     *
     * @return array
     */
    public function __debugInfo() {
        $formats = [];
        foreach ($this->_formats as $index => $format) {
            $formats[$index] = [
                'App\Model\Entity\Format' => [
                    'id' => $this->_formats[$index]->id,
                    'display_title' => $this->_formats[$index]->display_title,
                    'key()' => $this->_formats[$index]->key(),
                ]
            ];
        }
        return [
            '[_edition]' => [
                'App\Model\Entity\Edition' => [
                    'id' => $this->_edition->id,
                    'display_title' => $this->_edition->display_title,
                    'key()' => $this->_edition->key(),
                ]
            ],
            '[_formats]' => $formats,
            '[_provider_titles]' => $this->_provider_titles,
            '[_providers]' => '[\'edition\' => _edition] + _formats',
        ];
    }

}
