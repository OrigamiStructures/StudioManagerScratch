<?php
/**
 * @var \App\View\AppView $this
 * @var StackSet $artist
 * @var \App\Model\Entity\ArtistCard $artist
 * @var \App\Model\Entity\Manifest $manifest
 * @var \App\Model\Entity\DataOwner $dataOwner
 */
?>
<p>&nbsp;&nbsp;This may not be the correct data for this page but it brings the page online for further consideration.</p>

<p>&nbsp;&nbsp;What we should show is the one artistCard for the ID'd member, flanked by the data describing this
    users settings for thier relationship to this artist.</p>

<p>&nbsp;&nbsp;This is a potenial gateway page to various other pools of Artist data. It isn't clear to me what we
would need or want. But it's IMPORTANT AT THIS POINT not to get lost in implementation, espeicially
    of a user interface. The goal is to discover what kind of object we need and what interface they should have.</p>

<p>&nbsp;&nbsp;To this end, having the sequence of pages linked together should help identify what objects we need,
what other objects they need to help us access, and how they realate to one another. So, being able to move
through a sequence of stub display pages would give us a feel for the system without committing to a
final solution.</p>

<?php
//osd(get_class($artists));
//die;

foreach($artists->getData() as $artist) :
//    osd(get_class($artist));
    $manifest = $artist->manifest->element(0);
    $dataOwner = $artist->data_owner->element(0);
    $otherManagerCount = $artist->managers->count() - 1;
    $dispositionIDs = $artist->IDs('dispositions');
    ?>

    <?= $this->Html->tag('h1', $artist->rootDisplayValue()); ?>

    <?php if ($manifest->selfAssigned()) : ?>

    <?= $this->Html->para('', "You are the creator/owner of this aritst's "
        . "data and have identified $otherManagerCount "
        . "other managers for the data. View those details [here/make link]"); ?>

<?php else: ?>

    <?= $this->Html->para('', 'To request changes in your access to this '
        . 'artist, contact ' . $dataOwner->username() ); ?>

<?php endif; ?>

    <ul>
        <li>Contacts
            <ul>
                <?php foreach ($artist->contacts() as $contact) : ?>
                    <li><?= $contact ?></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            Addresses
            <ul>
                <?php foreach ($artist->addresses() as $address) : ?>
                    <li><?= $address ?></li>
                <?php endforeach; ?>
            </ul>
        </li>
    </ul>

    <?= 'The disposition ids are ' . \Cake\Utility\Text::toList($dispositionIDs); ?>

<?php endforeach; ?>
