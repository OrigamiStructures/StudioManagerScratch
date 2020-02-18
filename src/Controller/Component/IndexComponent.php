<?php


namespace App\Controller\Component;


use App\Model\Table\StacksTable;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

class IndexComponent extends Component
{

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    /**
     *
     * @return \Cake\Http\Response|null
     */
    public function build($seedQuery, $seedTarget, $pagingScope)
    {
        $this->block($seedQuery, $seedTarget, $pagingScope);
        $this->getController()->viewBuilder()->setLayout('index');
    }

    /**
     * Filter and Paginate a stackSet, create View variables for rendering
     *
     * @param $seedQuery Query The query that will produce the seed ids
     * @param $seedTarget string The 'TableAlias.seedName' for the stack query
     * @param $pagingScope string The 'pagingParams.scopeKey' to us for pagination
     */
    public function block($seedQuery, $seedTarget, $pagingScope) {

        list($StackTable, $seedName) = $this->parseTarget($seedTarget);
        $pagingParams = $this->getPagingParams($pagingScope);

        //sets search form vars and adds current post (if any) to query
        $this->getController()->userFilter($seedQuery);

        try {
            $stackSet = $this->getController()->paginate(
                $StackTable->pageFor($seedName, $seedQuery->toArray()),
                $pagingParams
            );
        } catch (NotFoundException $e) {
            return $this->getController()->redirect(
                $this->getController()->Paginator->showLastPage($pagingParams['scope'])
            );
        }

        $this->getController()->set('stackSet', $stackSet);
        $this->getController()->set('indexModel', $stackSet->getPaginatedTableName());
    }

    /**
     * Produce the StackTable instance and seed name for a filtered, paginated stack query
     *
     * Filtered, paginated queries are similiar, but act different stacks
     * and the seeds for the query may be on any of the seed types supported
     * by the stack. So the call is made with a param that names both the
     * StackTable and seed. These values are sent as a `dot` delimited string.
     *
     * This method validates the values and returns an array containing
     * the table instance and the seed name string.
     *
     * @param $seedTarget string A 'TableAlias.seedName'
     * @return array [StackTableInstance, 'seedName']
     */
    private function parseTarget($seedTarget)
    {
        list($tableAlias, $seedName) = explode('.', $seedTarget);
        $table = TableRegistry::getTableLocator()->get($tableAlias);
        /* @var StacksTable $table */

        return [$table, $seedName];

    }

    /**
     * Get current prefs-settings for 'paging' and add a scope key to it
     *
     * The `pagingParms` key must name one of the Preferences schema json
     * blocks that define a user customizable paging config block.
     *
     * The `scopeKey` can be any string you want to identify the role of
     * the paginated records. It will show up in the query params of the url.
     * If you set it to 'member_candidate' your urls would like like this:
     * `clearstudio.com/cardfile/view/19?member_candidate[page]=4`
     *
     * @param $pagingScope string A 'pagingParams.scopeKey' to us for pagination
     * @return array
     */
    private function getPagingParams($pagingScope)
    {
        list($pagingParams, $scopeKey) = explode('.', $pagingScope);
        $params = $this->getController()->Prefs->getPagingAttrs($pagingParams);
        $params['scope'] = $scopeKey;
        return $params;
    }

}
