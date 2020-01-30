<?php
namespace App\Model\Lib;


use Cake\Utility\Text;

trait ActorParamValidator
{

    /**
     * You can modify this public property to change the allowed arguments
     *
     * @var array The choices that will pass
     */
    public $validActors = [
        'artist', 'manager', 'supervisor'
    ];

    /**
     * Convert the string to lower case and verify it is a known value
     *
     * @param string $actor
     * @return string The validated string in lower case
     * @throws \BadMethodCallException
     */
    private function validateActor($actor) {
        $validActor = strtolower($actor);
        if (!in_array($validActor, $this->validActors)) {
            $this->badActor($validActor);
        }
        return $validActor;
    }

    /**
     * Common Exception point when an invalid actor is referenced
     *
     * @param string $actor
     * @throws \BadMethodCallException
     */
    private function badActor($actor) {
        $message = "$actor is not a valid actor focus point. Choose "
            . Text::toList($this->validActors, 'or');
        throw new \BadMethodCallException($message);
    }

}
