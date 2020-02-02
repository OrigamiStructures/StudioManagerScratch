<?php
use App\Lib\Wildcard;

if (isset($contextUser) && $contextUser->isSupervisorAlias()) {
    $clearLink = $this->Html->link('Clear', ['controller' => 'supervisors', 'action' => 'clearAlias']);
    echo $this->Html->para('alias warning',
        Wildcard::bracket($clearLink, '[&nbsp&nbsp]') . ' '
        . Wildcard::bracket(
            'You are acting as the supervisor ' . $contextUser->getCard('supervisor')->name(),
            '!*  *!'
        )
    );
}

