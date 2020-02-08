<?php


namespace App\Form;


use App\Lib\Wildcard;
use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\ORM\TableRegistry;

class CardfileFilter extends Form
{

    public function __construct(EventManager $eventManager = null)
    {
        parent::__construct($eventManager);
    }

    /**
     * A hook method intended to be implemented by subclasses.
     *
     * You can use this method to define the schema using
     * the methods on Cake\Form\Schema, or loads a pre-defined
     * schema from a concrete class.
     *
     * @param \Cake\Form\Schema $schema The schema to customize.
     * @return \Cake\Form\Schema The schema to use.
     */
    protected function _buildSchema(\Cake\Form\Schema $schema)
    {
        $MembersTable = TableRegistry::getTableLocator()->get('Members');
        return $MembersTable->getSchema();
    }

    public function execute(array $post)
    {
        parent::execute($post);
        // handle the user request to filter the index page
        $conditions = [];
        foreach (['first', 'last'] as $key) {
            $input = $post["{$key}_name"];
            if (!empty($input)) {
                $conditions += $this->condition($key, $input, $post);
            }
        }
        if (!empty($conditions)){
            // @todo make this and/or responsive
            $whereThis = ['OR' => $conditions];
            // modify the the query
        }
        return $whereThis;
    }
    /**
     * Construct a single condition from user search
     * @param $key
     * @param $input
     * @param $data
     * @return array
     */
    private function condition($key, $input, $data)
    {
        switch ($data["{$key}_name_mode"]) {
            case 0: //is
                $condition = ["{$key}_name" => $input];
                break;
            case 1: //starts
                $condition = ["{$key}_name LIKE" => Wildcard::after($input)];
                break;
            case 2: //ends
                $condition = ["{$key}_name LIKE" => Wildcard::before($input)];
                break;
            case 3: //contains
                $condition = ["{$key}_name LIKE" => Wildcard::wrap($input)];
                break;
            case 4: //isn't
                $condition = ["{$key}_name !=" => "$input"];
                break;
            default:
                $condition = [];
                break;
        }
        return $condition;
    }


}
