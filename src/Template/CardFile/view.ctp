<?php
use App\Model\Lib\Layer;
use App\Model\Entity\PersonCard;
use App\Model\Lib\ContextUser;
use App\Model\Lib\LayerAccessProcessor;
use App\View\AppView;

/**
 * @var AppView $this
 * @var ContextUser $contextUser
 * @var Layer $localSupervision
 * @var PersonCard $personCard
 */

//if ($personCard->hasManifests()) {
//    $receivedManagement = $personCard->receivedManagement($contextUser->getId('supervisor'));
//    $delegatedManagement = $personCard->delegatedManagement($contextUser->getId('supervisor'));
//}

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

?>

<?=
$this->Html->link('Mixed Cards', ['action' => 'index'])
. ' | ' . $this->Html->link('People', ['action' => 'people'])
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
    <?php if ($personCard->isSupervisor()) :
        echo $this->element('CardFile/view_supervisor');
    endif ?>

    <?php if ($personCard->isManager()) :
        echo $this->element('CardFile/view_manager');
    endif ?>

    <?php if ($personCard->isArtist()) : ?>
        <p><em><strong>This Artist's Works</strong></em></p>
        <?= $this->Html->nestedList($personCard->artworks->toKeyValueList('id', 'title')); ?>
    <?php endif ?>
<?php
echo "<p><strong>Memberships</strong></p>";
if (count($personCard->getMemberships()) == 0) { echo '<p>None</p>'; }
foreach ($personCard->getMemberships()->toArray() as $membership) {
    echo '<p>' . $this->Html->link($membership->name(), ['action' => 'view', $membership->id]) . '</p>';
}

