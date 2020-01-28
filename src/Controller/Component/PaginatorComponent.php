<?php


namespace App\Controller\Component;

use Cake\Controller\Component\PaginatorComponent as CorePaginator;
use Cake\Controller\ComponentRegistry;


class PaginatorComponent extends CorePaginator
{
    public $components = ['Flash'];

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    /**
     * Redirect to last page when request exceeds page limit
     *
     * @return array $url the params to re-render the page
     */
    public function showLastPage()
    {
        $qParams = $this->getController()->getRequest()->getQueryParams();
        $reqPage = $qParams['page'];
        $lastPage = $this->getController()->getRequest()->getParam('paging')['Identities']['pageCount'];
        if ($lastPage > 1) {
            $qParams['page'] = $lastPage;
        } else {
            unset($qParams['page']);
        }
        $url = [
            'controller' => $this->getController()->getRequest()->getParam('controller'),
            'action' => $this->getController()->getRequest()->getParam('action'),
            '?' => $qParams
        ];
        $this->Flash->error("Redirected to page $lastPage. Page $reqPage did not exist.");
        return $url;
    }


}
