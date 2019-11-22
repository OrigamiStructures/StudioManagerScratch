<?php

namespace App\Events;

use Cake\Error\Debugger;
use Cake\Event\EventListenerInterface;
use Cake\Mailer\Email;

class EventNotifier implements EventListenerInterface
{

    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * ### Example:
     *
     * ```
     *  public function implementedEvents()
     *  {
     *      return [
     *          'Order.complete' => 'sendEmail',
     *          'Article.afterBuy' => 'decrementInventory',
     *          'User.onRegister' => ['callable' => 'logRegistration', 'priority' => 20, 'passParams' => true]
     *      ];
     *  }
     * ```
     *
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            'Users.Component.UsersAuth.afterRegister' => 'afterRegisterEmail',
            'Users.Component.UsersAuth.beforeRegister' => 'beforeRegisterEmail'
        ];
    }

    public function afterRegisterEmail($event)
    {
        $email = new Email('default');
        $email->from(['me@example.com' => 'My Site'])
            ->to('jason@tempestinis.com')
            ->subject('Test')
            ->send('My message');
//        osdLog($email, 'After Register Email');
//        osd($event, 'afterRegisterEmail event');die;

    }
    public function beforeRegisterEmail($event)
    {
//        osd($event, 'beforeRegisterEmail event');die;
    }
}
