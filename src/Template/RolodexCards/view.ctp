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
?>

<?= $this->Html->link('Index page', ['action' => 'index']) ?>

<?php
//<editor-fold desc="Contact and Address variable creation">
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
//</editor-fold>
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
    /**
     * This is the identity card some registered user uses for their Superviors role
     */
    if ($personCard->isSupervisor()) :
        if($personCard->isManagementDelegate($contextUser->getId('supervisor'))) : ?>
        <p>is supervosor</p>
        <p><em><strong>Delegated Management</strong></em><br/></p>
            <?php
            $delegatedMessage = '<p>%s assigned management of the artist %s to %s.<br/> ' .
                '[Review details] [Contact %s].</p>';
            foreach ($delegatedManagement as $manifest) {
                /* @var \App\Model\Entity\Manifest $manifest */
                $supervisor = $names[$manifest->getSupervisorMember()];
                $manager = $names[$manifest->getManagerMember()];
                $artist = $names[$manifest->artistId()];
                printf($delegatedMessage, $supervisor, $artist, $manager, $manager);
            }
            ?>
        <?php endif; ?>
    <?php endif ?>

<?php
/** This is the identity card some registered user uses for their Manager role */
?>
    <?php if ($personCard->isManager()) : ?>
        <p>is manager</p>
        <p><em><strong>Received Management</strong></em>
            [Act as this manager now] </p>
        <?php
        $receivedMessage = '<p>%s assigned %s management of the artist %s.<br/> ' .
            '[Work on this artist now] [Contact %s].</p>';
        foreach ($receivedManagement as $manifest) {
            /* @var \App\Model\Entity\Manifest $manifest */
            $supervisor = $names[$manifest->getSupervisorMember()];
            $manager = $names[$manifest->getManagerMember()];
            $artist = $names[$manifest->artistId()];
            printf($receivedMessage, $supervisor, $manager, $artist, $supervisor);
        }
        ?>
    <?php endif ?>

    <?php if ($personCard->isArtist()) : ?>
        <p><em><strong>This Artist's Works</strong></em></p>
        <?= $this->Html->nestedList($personCard->artworks->toKeyValueList('id', 'title')); ?>
    <?php endif ?>

