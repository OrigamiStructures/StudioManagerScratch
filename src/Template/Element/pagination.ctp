<?php
/* @var \App\View\AppView $this */

use App\Form\PrefCon;

/**
 * PreferencesHelper buids the form, displaying current user variant values
 * in a view block.
 *
 * @todo The plan is still tentative, but currently, we can
 *      call the helper method appropriate for the index page. This method name
 *      could be set on a propery in the Form object, passed separately, or
 *      deduced from request context.
 *
 * @todo Possibly the Prefs form should be separate from the Paginator tools?
 *
 * @var \App\Form\LocalPreferencesForm $prefsForm
 * @var \App\Model\Entity\Preference $prefs
 */
//$this->Preferences->peoplePagination($prefsForm->asContext($prefs->user_id));

$formContext = $prefsForm->asContext($prefs->user_id);

echo $this->Form->create($formContext, [
    'url' => ['controller' => 'preferences', 'action' => 'setPrefs']
]);
echo $this->Html->tag(
    'ul',
    $this->Form->control(PrefCon::PAGINATION_LIMIT)
    . $this->Form->control(
        PrefCon::PAGINATION_SORT_PEOPLE, [
        'options' => $formContext->selectList(PrefCon::PAGINATION_SORT_PEOPLE),]),
    ['class' => 'menu']
);
echo $this->Form->control('id', ['type' => 'hidden']);
//echo $this->getView()->fetch('additional_controls');
echo $this->Form->submit();
echo $this->Form->end();
//echo $this->fetch('prefs_form');
