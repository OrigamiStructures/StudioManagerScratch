<?php
/**
 * Created by PhpStorm.
 * User: jasont
 * Date: 2019-02-18
 * Time: 18:29
 */

namespace App\Log\Engine;


use Cake\Log\Engine\BaseLog;

class ChangeTrackingLog extends BaseLog
{

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        // TODO: Implement log() method.
    }
}