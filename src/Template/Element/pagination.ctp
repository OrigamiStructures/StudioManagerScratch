<?php
use App\Form\PrefCon;
/**
 * @var \App\View\AppView $this
 * @var \App\Form\LocalPreferencesForm $prefsForm
 * @var \App\Model\Entity\Preference $prefs
 */

echo $this->Form->create($prefsForm->asContext($prefs->user_id), [
    'url' => ['controller' => 'preferences', 'action' => 'setPrefs']
]);
echo $this->Html->tag(
    'ul',
    '<li>' .
    $this->Form->control(PrefCon::PAGINATION_LIMIT) .
    '</li>' .
    '<li>' .
    $this->fetch('pagination_sort_preference_control') .
    '</li>',
    ['class' => 'menu']
);
echo $this->Form->control('id', ['type' => 'hidden']);
echo $this->Form->submit();
echo $this->Form->end();
