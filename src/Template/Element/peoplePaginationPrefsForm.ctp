<?php
/* @var \App\View\AppView $this */

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
echo $this->fetch('peoplePaginationPrefsForm');
