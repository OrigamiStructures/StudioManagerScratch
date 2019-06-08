<h1>Issues to address</h1>
<p>- Do we actually need dispositions in this stack? Or can that be gathered 
 on a dedicated API request without a listing here? The only reason 
 to list here would be for a select list.</p>
<p>- Artwork actually SHOULD be in the stack. But it isn't right now because 
 of the need to transition artwork linking. It is currently linked to the 
 user_id, but with the new Artist/Manifest system, I think it needs to 
 be linked to the Member that has the Manifest.</p>
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

<?= 'The disposition ids are ' . \Cake\Utility\Text::toList($dispositionIDs); ?>
		
<?php endforeach; ?>
