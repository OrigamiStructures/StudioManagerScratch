<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Lib\StackSet $artists
 * @var \App\Model\Entity\ArtistCard $artist
 * @var \App\Model\Entity\Manifest $manifest
 * @var \App\Model\Entity\DataOwner $dataOwner
 */
?>

<p>&nbsp;&nbsp;What we should show is the one artistCard for the ID'd member, flanked by the data describing this
    users settings for their relationship to this artist.</p>

<ul>
    <li>Management agreements sent issued but this user to other users</li>
    <li>The Management agreement that enables this user to manage this artist
    <ul>
        <li>Permission settings (current) and access to tools to change them</li>
    </ul></li>
</ul>

<p>&nbsp;&nbsp;This is a potenial gateway page to various other pools of Artist data. It isn't clear to me what we
would need or want. But it's IMPORTANT AT THIS POINT not to get lost in implementation, especially
    of a user interface. The goal is to discover what kind of object we need and what interface they should have.</p>

<p>&nbsp;&nbsp;To this end, having the sequence of pages linked together should help identify what objects we need,
what other objects they need to help us access, and how they realate to one another. So, being able to move
through a sequence of stub display pages would give us a feel for the system without committing to a
final solution.</p>

<?php
//osd(get_class($artists));
//die;

foreach($artists->getData() as $artist) :
    $manifest = $artist->getManifests()->element(0);
    $dataOwner = $artist->data_owner->element(0);
    $managmentDelegation = $artist->delegatedManagement($contextUser->getId('supervisor'));
    $dispositionIDs = $artist->IDs('dispositions');
    ?>

    <?= $this->Html->tag('h1', $artist->rootDisplayValue()); ?>

    <?php if ($manifest->selfAssigned()) : ?>

    <?= $this->Html->para('', "You are the creator/owner of this aritst's "
        . "data and have identified " . count($managmentDelegation)
        . " other managers for the data. View those details ") ?>

<?php else: ?>

    <?= $this->Html->para('', 'To request changes in your access to this '
        . 'artist, contact ' . $dataOwner->username() ); ?>

<?php endif; ?>

    <ul>
        <li>Contacts
            <ul>
                <?php foreach ($artist->getContacts()->toArray() as $contact) : ?>
                    <li><?= $contact->asString() ?></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            Addresses
            <ul>
                <?php foreach ($artist->getAddresses()->toArray() as $address) : ?>
                    <li><?= $address->asString() ?></li>
                <?php endforeach; ?>
            </ul>
        </li>
    </ul>

    <?= 'The disposition ids are ' . \Cake\Utility\Text::toList($dispositionIDs); ?>

<?php endforeach; ?>
<?php
$this->Form->fieldset();

$data = $artist->emitFormData();
?>

<?= $this->Form->create($data); ?>
<?= $this->element('Member/fieldset', ['type' => $data->member_type]); ?>

<?= '<fieldset><legend>Memberships</legend>'; ?>
<?php foreach ($data->memberships as $index => $membership) : ?>
    <?= $this->Form->control("memberships.$index.id", ['type' => 'hidden']); ?>
    <?php if ($membership->member_type === 'Person') : ?>
        <?= $this->Form->control("memberships.$index.first_name"); ?>
        <?= $this->Form->control("memberships.$index.last_name"); ?>
    <?php else: ?>
    <?= $this->Form->control("memberships.$index.last_name", ['label' => $membership->member_type]); ?>
    <?php endif; ?>
<?php endforeach; ?>
<?= '</fieldset>'; ?>

<?= '<section class="addresses fieldsets"><p>Addresses</p>'; ?>
<?php foreach ($data->addresses as $key => $address):?>
    <?= $this->element('Address/fieldset_hasManyAddresses', ['key' => $key]); ?>
<?php endforeach; ?>
<?= '</section>'; ?>

<?= '<section class="contacts fieldsets"><p>Addresses</p>'; ?>
<?php foreach ($data->addresses as $key => $address): ?>
    <?= $this->element('Contact/fieldset_hasManyContacts', ['key' => $key]); ?>
<?php endforeach; ?>
<?= '</section>'; ?>


<?= $this->Form->end(); ?>
