<?php
namespace App\Events;

use Cake\Event\Event;
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
     * @param $event Event
     */
    public function afterLoginAction($event)
    {
        /* @var UsersController $Users */
        $Users = $event->getSubject();
        $user = $Users->contextUser();
        $session = $Users->getRequest()->getSession();
        $session->write('User', serialize($user));
    }
}
