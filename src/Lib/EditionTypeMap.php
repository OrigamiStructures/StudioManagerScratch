<?php
namespace App\Lib;

/**
 * EditionTypeMap
 * 
 * @author dondrake
 */
class EditionTypeMap {
	
	
	public static function getNumberedTypes() {
		return [EDITION_LIMITED, PORTFOLIO_LIMITED, PUBLICATION_LIMITED];
	}
	
	public static function isNumbered($edition_type) {
		return in_array($edition_type, self::getNumberedTypes());
	}
	
	public static function getUnNumberedTypes() {
		return [EDITION_OPEN, PORTFOLIO_OPEN, PUBLICATION_OPEN];
	}
	
	public static function isUnNumbered($edition_type) {
		return in_array($edition_type, self::getUnNumberedTypes());
	}

	public static function getSingleFormatTypes() {
		return [EDITION_UNIQUE, EDITION_RIGHTS];
	}
	
	/**
	 * @todo should this be named isFlat or isUnique or isSimple
	 * 
	 * @param type $edition_type
	 * @return type
	 */
	public static function isSingleFormat($edition_type) {
		return in_array($edition_type, self::getUnNumberedTypes());
	}
	
	public static function getMultiFormatEditionTypes() {
		return [EDITION_LIMITED, PORTFOLIO_LIMITED, PUBLICATION_LIMITED,
				EDITION_OPEN, PORTFOLIO_OPEN, PUBLICATION_OPEN];
	}
	
	public static function isMultiFormat($edition_type) {
		return in_array($edition_type, self::getMultiFormatEditionTypes());
	}

	public static function isValid($type) {
		return in_array($type, [EDITION_UNIQUE, EDITION_RIGHTS, EDITION_LIMITED, 
			PORTFOLIO_LIMITED, PUBLICATION_LIMITED,
			EDITION_OPEN, PORTFOLIO_OPEN, PUBLICATION_OPEN]);
	}
	
}
