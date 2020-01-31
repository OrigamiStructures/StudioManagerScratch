<?php
if (isset($contextUser) && $contextUser->isSupervisorAlias()) {
    $clearLink = $this->Html->link('[Clear] ', ['controller' => 'supervisors', 'action' => 'clearAlias']);
    echo $this->Html->para('alias warning',
        $clearLink . ' !* You are acting as the supervisor ' . $contextUser->getCard('supervisor')->name() . ' *!'
    );
}

