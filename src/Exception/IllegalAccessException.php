<?php
namespace App\Exception;

use Cake\Core\Exception\Exception;

class IllegalAccessException extends Exception
{

    public function __construct($message, $code = 500, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
