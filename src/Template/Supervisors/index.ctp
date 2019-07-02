<?php

    $this->loadHelper('People');
    //osd($manifests);
    osd($currentUser->username());

    $assignedToForeign = $supervisorManifests
        ->find('manifest')
        ->specifyFilter('selfAssigned', FALSE)
        ->loadStacks();
		
?>
	<p>This stuff might be good in a table too. All this formatting 
		I played with hasn't done much to improve things</p>
	
    <h1>Supervisor Manifests</h1>

<?php

    foreach ($assignedToForeign as $supervisorManifest) : ?>

    <?= $this->People->manifestSummary($supervisorManifest); ?>

<?php endforeach; ?>

    <h1>Manager Manifests</h1>

<?php foreach ($managerManifests->load() as $managerManifest) : ?>

    <?= $this->People->manifestSummary($managerManifest); ?>

<?php endforeach; ?>
	
<?php
	
//	osd($managers);
	$collection = collection($supervisorManifests->load());
	
	$assignments = $collection->reduce(function($accum, $entity) {
		$accum[$entity->rootElement()->manager_id] =
			$entity->managerCard()->name();
		return $accum;
	}, []);
	
	$ownedArtists = $collection->reduce(function($accum, $entity) {
		$accum[$entity->rootElement()->member_id] =
			$entity->artistCard()->name();
		return $accum;
	}, []);
	
	$collection = collection($managerManifests->load());
	
	$allArtists = $collection->reduce(function($accum, $entity) {
		$accum[$entity->rootElement()->member_id] =
			$entity->artistCard()->name();
		return $accum;
	}, []);
	
?>
	<?= $this->Form->create(null, ['action' => '/manager']); ?>
	<p>Edit assignments to a manager</p>
	<?= $this->Form->select(
			'assignments', 
			$assignments, 
			['empty' => 'Choose a manager']
		); ?>
	<?= $this->Form->button('Submit'); ?>
	<?= $this->Form->end(); ?>
	
	<?= $this->Form->create(null, ['action' => '/artist']); ?>
	<p>Edit management agreements for an artist</p>
	<?= $this->Form->select(
			'owned_artists', 
			$ownedArtists, 
			['empty' => 'Choose an artist']
		); ?>
	<?= $this->Form->button('Submit'); ?>
	<?= $this->Form->end(); ?>
	
	<?= $this->Form->create(); ?>
	<p>Manage your artists (link to some index page)</p>
	<?= $this->Form->input(
			'manager_id', 
	['type' => 'hidden', 'value' => $currentUser->managerId()]); ?>
	<?= $this->Form->button('Submit'); ?>
	<?= $this->Form->end(); ?>
	
	<?= $this->Form->create(); ?>
	<p>Manage an artist</p>
	<?= $this->Form->select(
			'all_artists', 
			$allArtists, 
			['empty' => 'Choose an artist']
		); ?>
	<?= $this->Form->button('Submit'); ?>
	<?= $this->Form->end(); ?>


