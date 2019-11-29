<?php
namespace App\Events;

use Cake\Http\Client\Request;
use OSDebug;
use Cake\Event\EventListenerInterface;
use Cake\Mailer\Email;
use App\Controller\UsersController;

/**
 * Class UserNotifier
 * @package App\Events
 * @property Request $request
 */
class UserNotifier implements EventListenerInterface
{


    /**
     * @inheritDoc
     */
    public function implementedEvents()
    {
        return [
            'Users.Component.UsersAuth.afterLogin' => 'afterLoginAction'
        ];
    }

    /**
     * Write login user object to session after login
     *
     * @param $event
     * @property UsersController $Users
     */
    public function afterLoginAction($event)
    {
        $Users = $event->_subject;
        $user = $Users->contextUser();
        $session = $Users->getRequest()->getSession();
        $session->write('User', serialize($user));
    }
}
