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

	$allNonArtistList =
        $myPersonCards
            ->find('identity')
            ->specifyFilter('is_artist', 1, '!=')
            ->loadKeyValueList('id', 'name');
	osd($allNonArtistList);die;
?>
	<?= $this->Form->create(null, ['action' => '/manager']); ?>
	<p>Supervise manager (edit the artists assigned to a manager and the permissions for each of those artists)</p>
	<?= $this->Form->select(
			'assignments', 
			$assignments, 
			['empty' => 'Choose a manager']
		); ?>
	<?= $this->Form->button('Submit'); ?>
	<?= $this->Form->end(); ?>
	
	<?= $this->Form->create(null, ['action' => '/artist']); ?>
	<p>Supervise artist (edit the managers assigned to an artist, and the permissions for each of those managers)</p>
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

	<?= $this->Form->create(); ?>
	<p>Create an artist</p>
	<?= $this->Form->select(
			'From',
			$allArtists,
			['empty' => 'New']
		); ?>
	<?= $this->Form->button('Submit'); ?>
	<?= $this->Form->end(); ?>




