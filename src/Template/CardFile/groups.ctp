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

    $shares = '';
    if ($card->hasPermittedManagers()) {
        $managers = collection($card->getLayer('shares')->toArray())
            ->reduce(function($accum, $share) {
                /* @var \App\Model\Entity\Share $share */

                $accum[] = $share->getName('manager');
                return $accum;
            }, []);
        $shares =  "<p><strong>Managers permitted to see {$card->name(LABELED)}</strong>: "
            . Text::toList($managers) . '</p>';
    }

    echo "<p><strong>" . $this->Html->link($card->name(), ['action' => 'view', $card->rootID()]) . "</strong></p>";
    echo $memberships;
    echo $members;
    echo $shares;


}

echo $this->element('Member/search', ['identitySchema' => $identitySchema]);
