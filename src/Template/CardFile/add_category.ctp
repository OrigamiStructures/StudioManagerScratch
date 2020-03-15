<?php
/* @var \App\View\AppView $this */
/* @var \App\Model\Lib\StackSet $managerDelegates */
/* @var \App\Model\Entity\PersonCard $delegate */

echo $this->Html->tag('h1', 'New Category');

echo $this->Form->create($member);
echo $this->Form->control('last_name', ['label' => 'Name']);

/**
 * Delegated Managers permission assignment
 */
echo $this->Html->para('', 'Categories can be made visible to your managers. ' .
    'This will give them access to the people and organizations in the category. ' .
    '<strong>Check the managers you want to allow to see this category</strong>.');

foreach ($managerDelegates->getLayer('identity')->toArray() as $delegate) {
    echo $this->Form->control("permit.$delegate->id", [
        'type' => 'checkbox',
        'label' => ' ' . $delegate->name()
    ]);
}

echo $this->Form->control('user_id', ['type' => 'hidden']);
echo $this->Form->submit();
echo $this->Form->end();
