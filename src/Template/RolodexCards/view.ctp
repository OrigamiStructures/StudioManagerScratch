<?php
use App\Model\Lib\Layer;
use App\Model\Entity\PersonCard;
use App\Model\Lib\ContextUser;
use App\Model\Lib\LayerAccessProcessor;

/* @var View $this */
/* @var ContextUser $contextUser */
/* @var Layer $localSupervision */
/* @var PersonCard $personCard */
?>

<?= $this->Html->link('Index page', ['action' => 'index']) ?>

<?php
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
    if ($personCard->isSupervisor()) : ?>

        <p>This section should respond to whether this is the record of the registered user
    or of a foreign user. In one case the user would be able to adjust delegations
    they had made. In the other case, the user would be able to see who had made them
    a manager, and what artists they had.<br/>
    <?php
    ?>
            This supervisor has delegated artist management to <?= ''/*count($delegateManagement)*/ ?> Managers<br/>
        <?= $this->Html->link('Review Delegated Artist Management', ['action' => 'index']) ?><br/>
    A message and a form with a button is probably what we need rather than a simple link.</p>
    <?php endif ?>

    <?php if ($personCard->isManager()) : ?>
        <?php
//        osd($delegatedManagement, 'delegated');
//        osd($receivedManagement, 'received');
        $delegatedMessage = '<p>%s assigned management of the artist %s to %s. Review details here [link].</p>';
        foreach ($delegatedManagement as $manifest) {
            /* @var \App\Model\Entity\Manifest $manifest */
            $supervisor = $names[$manifest->getSupervisorMember()];
            $manager = $names[$manifest->getManagerMember()];
            $artist = $names[$manifest->artistId()];
            printf($delegatedMessage, $supervisor, $artist, $manager);
        }
        ?>
        <p><?= $this->Html->link('Review Artist Management', ['action' => 'index']) ?></p>
    <?php endif ?>

    <?php if ($personCard->isArtist()) : ?>
        <p><?= $this->Html->link('Review Artworks for this Artist', ['action' => 'index']) ?></p>
    <?php endif ?>

