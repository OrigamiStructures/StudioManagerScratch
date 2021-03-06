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

/**
 * Contact and Address
 */
$primaryContact = $personCard->getLayer('contacts')
    ->find()
    ->specifyFilter('isPrimary', '1')
    ->toKeyValueList('id', 'asString');

$primaryAddress = $personCard->getLayer('addresses')
    ->find()
    ->specifyFilter('isPrimary', '1')
    ->toKeyValueList('id', 'asString');

$otherContacts = $personCard->getLayer('contacts')
    ->find()
    ->specifyFilter('isPrimary', 0)
    ->toKeyValueList('id', 'asString');

$otherAddresses = $personCard->getLayer('addresses')
    ->find()
    ->specifyFilter('isPrimary', 0)
    ->toKeyValueList('id', 'asString');

$con_add_format = '</br><span id="%s%s">%s</span>';

$members = '';
if ($personCard->hasMembers()) {
    $members =  "<p><strong>Members</strong>: "
        . Text::toList($personCard->getMembers()->toValueList('name')) . '</p>';
}


?>

<?=
$this->Html->link('Mixed Cards', ['action' => 'index'])
. ' | ' . $this->Html->link('Organizations', ['action' => 'organizations'])
?>

<h1><?= $personCard->rootElement()->name() ?></h1>

<p><em><strong>Primary contact and address</strong></em>
    <?php
    foreach($primaryContact as $id => $contact){
        printf($con_add_format, 'con-', $id, $contact);
    }
    foreach($primaryAddress as $id => $address){
        printf($con_add_format, 'adr-', $id, $address);
    }
    ?>
</p>
<p><em><strong>Other contacts and addresses</strong></em>
    <?php
    foreach($otherContacts as $id => $contact){
        printf($con_add_format, 'con-', $id, $contact);
    }
    foreach($otherAddresses as $id => $address){
        printf($con_add_format, 'adr-', $id, $address);
    }
    ?>
</p>

<?php
echo "<p><strong>Memberships</strong></p>";
if (count($personCard->getMemberships()) == 0) { echo '<p>None</p>'; }
foreach ($personCard->getMemberships()->toArray() as $membership) {
    echo '<p>' . $this->Html->link($membership->name(), ['action' => 'view', $membership->id]) . '</p>';
}

echo "<p><strong>Members</strong></p>";
if (!$personCard->hasMembers()) { echo '<p>None</p>'; }
foreach ($personCard->getMembers()->toArray() as $member) {
    echo '<p>' . $this->Html->link($member->name(), ['action' => 'view', $member->id]) . '</p>';
}
