<?php
use App\Model\Lib\Layer;
use App\Model\Entity\PersonCard;
use App\Model\Lib\ContextUser;
use App\Model\Lib\LayerAccessProcessor;
use App\View\AppView;
use Cake\Utility\Text;

/**
 * @var AppView $this
 * @var ContextUser $contextUser
 * @var Layer $localSupervision
 * @var PersonCard $personCard
 */

 echo $this->Html->link('Mixed Cards', ['action' => 'index'])
    . ' | ' . $this->Html->link('Categories', ['action' => 'groups'])
?>

    <h1><?= $personCard->rootElement()->name() ?></h1>

<?php

echo "<p><strong>Memberships</strong></p>";
if (count($personCard->getMemberships()) == 0) {
    echo '<p>None</p>';
} else {
    echo '<ul class="member">';
    foreach ($personCard->getMemberships()->toArray() as $membership) {
        echo '<li>' . $this->Html->link($member->name(), ['action' => 'view', $member->id]) . '</li>';
    }
    echo '</ul>';
}

echo "<p><strong>Members</strong></p>";
if (!$personCard->hasMembers()) {
    echo '<p>None</p>';
} else {
    echo '<ul class="member">';
    foreach ($personCard->getMembers()->toArray() as $member) {
        echo '<li>' . $this->Html->link($member->name(), ['action' => 'view', $member->id]) . '</li>';
    }
    echo '</ul>';
}

echo "<p><strong>Share with</strong></p>";
if (!$personCard->hasPermittedManagers()) {
    echo '<p>None</p>';
} else {
    echo '<ul class="member">';
    foreach ($personCard->getPermittedManagers() as $member) {
        echo '<li>' . $member . '</li>';
//        echo '<li>' . $this->Html->link($member->name(), ['action' => 'view', $member->id]) . '</li>';
    }
    echo '</ul>';
}



