<?php
use Cake\Utility\Text;
use Cake\Form\Form;

/* @var \App\View\AppView $this */

/**
 * prepate the pagination prefs form view block for use by the layout
 */
echo $this->element('Preferences/Pagination/category');

echo $this->Html->link('New Category', ['action' => 'add', 'category']);

foreach($categoryCards->getData() as $id => $card) {

    /* @var \App\Model\Entity\CategoryCard $card */

    $memberships = '';
    if ($card->isMember()) {
        $memberships =  "<p><strong>Memberships</strong>: "
            . Text::toList($card->getMemberships()->toValueList('name')) . '</p>';
    }

    $members = '';
    if ($card->hasMembers()) {
        $members =  "<p><strong>Members</strong>: "
            . Text::toList($card->getMembers()->toValueList('name')) . '</p>';
    }

    echo "<p><strong>" . $this->Html->link($card->name(), ['action' => 'view', $card->rootID()]) . "</strong></p>";
    echo $memberships;
    echo $members;


}

echo $this->element('Member/search', ['identitySchema' => $identitySchema]);
