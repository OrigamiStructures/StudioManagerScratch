<h1>Issues to address</h1>
<p>- Do we actually need dispositions in this stack? Or can that be gathered
 on a dedicated API request without a listing here? The only reason
 to list here would be for a select list.</p>
<p>- I've done a first-approximation inclusion of Artworks by adding an
 artist_id link to 'artworks' (which actually is a link to member_id and
should probably be renamed). </p>
<p>&nbsp;&nbsp;
	The role of a linking list like artworks is to speed up user access to
	 the data they want. Would we want the choice-grain at this level to go
	 all the way to Editions?</p>
<p>&nbsp;&nbsp;
	I think that's probably not necessary since it adds complexity that only
	benefits the subset of users that are heavy edition creators; a minority.</p>
<p>- Duplicate addresses and contacts may be a problem (as they are in this case).
 When a relevant stack is created (rather than loaded from cache) a check could be
 made and the stack flagged for editing. Is this a kind of data management service
 we'd want to provide? Or a time-of-create/edit check could be done as an alternative.
 Preventative vs corrective strategies.</p>
<p>
    I see the usage of dispositions as a small block of n past dispos and n future dispos,
    organized by date, that are a part of a standard data packet. They should always include
    a 'more...' link so get more or to get to detail.
</p>
<?php
//osd($artists);
//die;
/* @var \App\View\AppView $this */
/* @var \App\Model\Entity\Manifest $manifest */
/* @var \App\Model\Entity\DataOwner $dataOwner */
/* @var \App\Model\Entity\ArtistCard $artist */

foreach($artists->getData() as $artist) :
	$manifest = $artist->manifest->element(0);
	$dataOwner = $artist->data_owner->element(0);
	$otherManagerCount = $artist->managers->count() - 1;
	$dispositionIDs = $artist->IDs('dispositions');
?>

<?= $this->Html->tag('h1', $artist->rootDisplayValue()); ?>

<?php if ($manifest->selfAssigned()) : ?>

<?= $this->Html->para('', "You are the creator/owner of this aritst's "
			. "data and have identified $otherManagerCount "
			. "other managers for the data. View those details "
            . $this->Html->link('here', ['controller' => 'artists', 'action' => 'view', $artist->rootID()])
    ); ?>

<?php else: ?>

    <?= $this->Html->para('', 'To request changes in your access to this '
        . 'artist, contact ' . $dataOwner->username() . '. Email link here?'); ?>
    <?= $this->Html->para('', 'But shouldn\'t we still be able to go to a detail page?'); ?>

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
