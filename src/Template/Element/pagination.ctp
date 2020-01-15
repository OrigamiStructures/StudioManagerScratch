<?php
echo $this->Html->tag('ul',
    $this->Paginator->prev() . '<li>||</li>' . $this->Paginator->next(),
    ['class' => 'menu']);

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
$this->Preferences->peoplePagination($prefsForm->asContext($prefs->user_id));
echo $this->fetch('prefs_form');
