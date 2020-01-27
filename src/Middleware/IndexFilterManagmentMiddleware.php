<?php


namespace App\Middleware;

use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexFilterManagmentMiddleware
{

    protected $scopes = [
        'Cardfile.organizations' => [
            'Cardfile.organizations',
            'Cardfile.view',
            'Preferences.setPrefs',
            'Requests.view',
            'Panels.view']
    ];

    /**
     * Maintain content filters within sensible page scope
     *
     * On index pages, users can search to filter the content.
     *
     * There will be some set of related pages where the filter should continue
     * to exist (eg. index -> view/x -> index or index?page=2 -> index?page=3).
     *
     * This middleware looks for a saved filter and base on the current request
     * decides whether to keep or delete it.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param callable $next The next middleware to call.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        /* @var ServerRequest $request */
        /* @var Session $session */

        $session = $request->getSession();
        $filter = $session->read('filter');

        $requestPath = $request->getParam('controller') . '.' . $request->getParam('action');

        if (is_null($filter)) {
            return $next($request, $response);
        }
        if (!in_array($requestPath, $this->scopes[$filter['path']])) {
            $session->delete('filter');
        }
        return $next($request, $response);
    }
}
