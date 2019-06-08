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
<?php
//osd($artists);
//die;
foreach($artists->load() as $artist) :
	$manifest = $artist->manifest->element(0);
	$dataOwner = $artist->data_owner->element(0);
	$otherManagerCount = $artist->managers->count() - 1;
	$dispositionIDs = $artist->IDs('dispositions');
?>

<?= $this->Html->tag('h1', $artist->rootDisplayValue()); ?>
	
<?php if ($manifest->self()) : ?>
		
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

<?php osd($artist->artworks->find()->loadValueList('identityLabel')); ?>

<?= 'The disposition ids are ' . \Cake\Utility\Text::toList($dispositionIDs); ?>
		
<?php endforeach; ?>
