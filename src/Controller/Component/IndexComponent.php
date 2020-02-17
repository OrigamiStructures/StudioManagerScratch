<?php


namespace App\Controller\Component;


use App\Model\Table\StacksTable;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

class IndexComponent extends Component
{

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    /**
     *
     * @param $seedIdQuery The query which isolates the seed ids
     * @param $seedName The seed names, what we will distill on
     * @param $tableName The StackTable name to use
     * @param $pagingAttrName The name of the paging prefs set
     * @return \Cake\Http\Response|null
     */
    public function build($seedIdQuery, $seedName, $tableName, $pagingAttrName)
    {
        //sets search form vars and adds current post (if any) to query
        $this->getController()->userFilter($seedIdQuery);

        $table = TableRegistry::getTableLocator()->get($tableName);
        /* @var StacksTable $table */

        try {
            $stackSet = $this->getController()->paginate(
                $table->pageFor($seedName, $seedIdQuery->toArray()),
                $this->getController()->Prefs->getPagingAttrs($pagingAttrName)
            );
        } catch (NotFoundException $e) {
            return $this->getController()->redirect($this->getController()->Paginator->showLastPage());
        }

        $this->getController()->viewBuilder()->setLayout('index');
        $this->getController()->set('stackSet', $stackSet);
        $this->getController()->set('indexModel', $stackSet->getPaginatedTableName());

    }

}
