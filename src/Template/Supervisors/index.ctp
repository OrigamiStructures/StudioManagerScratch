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

