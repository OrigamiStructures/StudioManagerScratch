<?php
use App\Form\PrefCon;
/**
 * @var \App\View\AppView $this
 * @var \App\Form\LocalPreferencesForm $prefsForm
 * @var \App\Model\Entity\Preference $prefs
 */

$formContext = $prefsForm->asContext($prefs->user_id);

echo $this->Form->create($formContext, [
    'url' => ['controller' => 'preferences', 'action' => 'setPrefs']
]);
echo $this->Html->tag(
    'ul',
    '<li>' .
    $this->Form->control(PrefCon::PAGINATION_LIMIT) .
    '</li>' .
    '<li>' .
    $this->Form->control(
        PrefCon::PAGINATION_SORT_PEOPLE, [
        'options' => $formContext->selectList(PrefCon::PAGINATION_SORT_PEOPLE),]) .
    '</li>',
    ['class' => 'menu']
);
echo $this->Form->control('id', ['type' => 'hidden']);
echo $this->Form->submit();
echo $this->Form->end();
