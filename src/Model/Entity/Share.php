<?php
namespace App\Model\Entity;

use Cake\Error\Debugger;
use Cake\Log\Log;
use Cake\ORM\Entity;

/**
 * Share Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $supervisor_id
 * @property int|null $manager_id
 * @property int|null $category_id
 *
 * @property \App\Model\Entity\Supervisor $supervisor
 * @property \App\Model\Entity\Manager $manager
 * @property \App\Model\Entity\Category $category
 */
class Share extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'created' => true,
        'modified' => true,
        'supervisor_id' => true,
        'manager_id' => true,
        'category_id' => true,
        'user_id' => true,
        'supervisor' => true,
        'manager' => true,
        'category' => true
    ];

    /**
     * Get the member id of the specified actor
     *
     * @param $actor string a value in ActorParamValidator::validNames
     * @return int
     * @throws \BadMethodCallException
     */
    public function getMemberId($actor)
    {
//        $validActor = $this->validateActor($actor);
        $validActor = $actor;

        switch ($validActor) {
            case 'category':
                $result = $this->category_id;
                break;
            case 'manager':
                $result = $this->manager_id;
                break;
            case 'supervisor':
                $result = $this->supervisor_id;
                break;
        }
        return $result;
    }

    public function getNames()
    {
        return $this->names ?? [];
    }

    /**
     * If $this::names has been set, return the name of the actor
     *
     * $this::names will be set when Manifests is used in PersonCards
     *
     * @param $actor string a value in ActorParamValidator::validNames
     * @return string|null
     */
    public function getName($actor)
    {
//        $validActor = $this->validateActor($actor);
        $validActor = $actor;

        if (!isset($this->names)) {
            $trace = var_export(Debugger::trace(), true);
            Log::write(LOG_WARNING, "Manifest::getName($actor) was called but the Manifest
            has not been configured as a link-layer for a stack." . PHP_EOL . "The trace to this getName call: " .
                PHP_EOL . $trace);
            return null;
        }

        switch ($validActor) {
            case 'supervisor':
                $index = $this->getMemberId('supervisor');
                break;
            case 'manager':
                $index = $this->getMemberId(('manager'));
                break;
            case 'category':
                $index = $this->getMemberId('category');
                break;
        }
        return $this->names[$index];
    }


}
