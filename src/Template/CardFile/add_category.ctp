<?php
/* @var \App\View\AppView $this */
/* @var \App\Model\Lib\StackSet $managerDelegates */
/* @var \App\Model\Entity\PersonCard $delegate */

echo $this->Form->create($member);
echo $this->Form->control('last_name', ['label' => 'Name']);

/**
 * Delegated Managers permission assignment
 */

/**
 * Member assignment or defer to edit page (next, dedicated to that task?)
 */

echo $this->Form->end();
osd($managerDelegates);
