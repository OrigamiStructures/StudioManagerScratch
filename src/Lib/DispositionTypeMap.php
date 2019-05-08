<?php
namespace App\Lib;

/**
 * DispositionTypeMap
 * 
 * @author dondrake
 */
class DispositionTypeMap {

    public static function collectedDispositionTypes() {
        return [DISPOSITION_TRANSFER_SALE,  DISPOSITION_TRANSFER_SUBSCRIPTION,
            DISPOSITION_TRANSFER_DONATION,  DISPOSITION_TRANSFER_GIFT,
            DISPOSITION_TRANSFER_RIGHTS
        ];
    }
    public static function scrappedDispositionTypes() {
        return [DISPOSITION_UNAVAILABLE_LOST, DISPOSITION_UNAVAILABLE_DAMAGED,
            DISPOSITION_UNAVAILABLE_STOLEN, DISPOSITION_NFS];
    }

    public static function unavailableDispositionTypes() {
        return array_merge(self::collectedDispositionTypes(), self::scrappedDispositionTypes());
    }

}
