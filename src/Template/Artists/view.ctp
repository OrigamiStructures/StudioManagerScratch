<?php
/**
 * @var \App\View\AppView $this
 * @var StackSet $artist
 * @var \App\Model\Entity\ArtistCard $artist
 * @var \App\Model\Entity\Manifest $manifest
 * @var \App\Model\Entity\DataOwner $dataOwner
 */
?>
This may not be the correct data for this page but it brings the page online for further consideration.

What we should show is the one artistCard for the ID'd member, flanked by the data describing this
users settings for thier relationship to this artist.

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
