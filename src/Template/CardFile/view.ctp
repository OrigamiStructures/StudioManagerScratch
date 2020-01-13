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

if ($personCard->hasManifests()) {
    $receivedManagement = $personCard->receivedManagement($contextUser->getId('supervisor'));
    $delegatedManagement = $personCard->delegatedManagement($contextUser->getId('supervisor'));
}

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

/**
 * Manifests
 */
    /**
     * This card is either
     *  This supervisor's identity      (sup_id = sup_member = Person->rootId)
     *      show self artists           (sup_id = mgr_id = Person->ownerId && ! sup identity)
     *      show foreign artists
     *      show foreign supervisors
     *  A foreign supervisor's identity
     *      show foreign artists
     *  An aritist this supervisor created
     *      show foreign managers, show artwork, show permissions
     *  An artist a foreign supervisor created
     *      show artwork, show foreign manager
     */

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
    <?php
    if ($personCard->isSupervisor()) :
        if($personCard->delegatedManagement($contextUser->getId('supervisor'))) : ?>
        <p><em><strong>Delegated Management</strong></em></p>
            <?php
            $delegatedMessage = '<p>%s assigned management of the artist %s to %s. %s. [Contact %s].</p>';
            foreach ($delegatedManagement as $manifest) {
                /* @var \App\Model\Entity\Manifest $manifest */
                $permissonLink = $this->Html->link('Set Permissions', [
                    'controller' => 'supervisors', 'action' => 'permissions', $manifest->id
                ]);
                $supervisor = $manifest->getName('supervisor');
                $manager = $manifest->getName('manager');
                $artist = $manifest->getName('artist');
                printf($delegatedMessage, $supervisor, $artist, $manager, $permissonLink, $manager);
            }
            ?>
        <?php endif; ?>
    <?php endif ?>

    <?php if ($personCard->isManager()) : ?>
        <p><em><strong>Received Management</strong></em></p>
        <?php
        $receivedMessage = '<p>%s assigned %s management of the artist %s. [Work on this artist now]. [Contact %s].</p>';
        foreach ($receivedManagement as $manifest) {
            /* @var \App\Model\Entity\Manifest $manifest */
            $supervisor = $manifest->getName('supervisor');
            $manager = $manifest->getName('manager');
            $artist = $manifest->getName('artist');
            printf($receivedMessage, $supervisor, $manager, $artist, $supervisor);
        }
        ?>
    <?php endif ?>

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

