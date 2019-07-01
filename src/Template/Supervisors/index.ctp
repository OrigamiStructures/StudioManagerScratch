<?php
// This variable needs to be part of the helper 
// along with the fucntion(s) down below
$outputPattern = '<em style="font-weight: normal;">Artist:</em> %s '
		. '<span style="font-weight: normal; font-size: smaller;">'
		. '(<em>Manager:</em> %s has %s)'
		. '</span>';

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

<?php ;
foreach ($assignedToForeign as $supervisorManifest) : ?>

    <?= manifestSummary($supervisorManifest, $this->Html, $outputPattern); ?>

<?php endforeach; ?>

    <h1>Manager Manifests</h1>

<?php foreach ($managerManifests->load() as $managerManifest) : ?>

    <?= manifestSummary($managerManifest, $this->Html, $outputPattern); ?>

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
		
	<p>Edit assignments to a manager</p>
	<?= $this->Form->select('assignments', $assignments, ['empty' => 'Choose a manager']); ?>
	<p>Edit management agreements for an artist</p>
	<?= $this->Form->select('ownedArtists', $ownedArtists, ['empty' => 'Choose an artist']); ?>
	<p>Manage your artists (link to some index page)</p>
	<p>Manage an artist</p>
	<?= $this->Form->select('allArtists', $allArtists, ['empty' => 'Choose an artist']); ?>
	

<?php  
/**
 * This should move to a helper
 * 
 * @param type $manifest
 * @param type $helper
 * @param type $outputPattern
 * @return type
 */
function manifestSummary($manifest, $helper, $outputPattern) {
	
	$artistName = $manifest->artistCard()->rootDisplayValue();
    $managerName = $manifest->selfAssigned() 
			? 'self' 
			: $manifest->managerCard()->rootDisplayValue();
    $access = $manifest->accessSummary();
	
	return $helper->tag(
			'h3', 
			sprintf($outputPattern, $artistName, $managerName, $access),
			['escape' => FALSE]);
}

?>

