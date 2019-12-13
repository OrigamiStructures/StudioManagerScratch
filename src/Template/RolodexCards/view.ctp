<?php /* @var View $this */ ?>
<?= $this->Html->link('Index page', ['action' => 'index']) ?>
<?php
use App\Model\Lib\Layer;
/* @var \App\Model\Entity\PersonCard $personCard */

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
 *
 * @var \App\Model\Lib\ContextUser $contextUser
 */
if(count($personCard->getManifests()) > 0) {

    $localSupervision = $personCard->getLayer('manifests')
        ->find()
        ->specifyFilter('supervisor_id', $contextUser->getId('supervisor'))
        ->toArray();

    $foreignSupervision = $personCard->getLayer('manifests')
        ->find()
        ->specifyFilter('supervisor_id', $contextUser->getId('supervisor'), '!=')
        ->toArray();

}
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
    This supervisor has delegated artist management to <?= ''/*count($delegateManagement)*/ ?> Managers<br/>
        <?= $this->Html->link('Review Delegated Artist Management', ['action' => 'index']) ?><br/>
    A message and a form with a button is probably what we need rather than a simple link.</p>
    <?php endif ?>

    <?php if ($personCard->isManager()) : ?>
        <p><?= $this->Html->link('Review Received Artist Management', ['action' => 'index']) ?></p>
    <?php endif ?>

    <?php if ($personCard->isArtist()) : ?>
        <p><?= $this->Html->link('Review Artworks for this Artist', ['action' => 'index']) ?></p>
    <?php endif ?>

