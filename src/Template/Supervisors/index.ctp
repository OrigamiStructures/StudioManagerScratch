<?php
//osd($manifests);
osd($currentUser->username());

$assignedToForeign = $supervisorManifests
    ->find('manifest')
    ->specifyFilter('selfAssigned', FALSE)
    ->loadStacks();
		
$outputPattern = '<em style="font-weight: normal;">Artist:</em> %s '
		. '<span style="font-weight: normal; font-size: smaller;">'
		. '(<em>Manager:</em> %s has %s)'
		. '</span>';

?>
	<p>This stuff might be good in a table too. All this formatting 
		I played with hasn't done much to improve things</p>
    <h1>Supervisor Manifests</h1>

<?php ;
foreach ($assignedToForeign as $supervisorManifest) :
	$artistName = $supervisorManifest->artistCard()->rootDisplayValue();
    $managerName = $supervisorManifest->managerCard()->rootDisplayValue();
    $access = $supervisorManifest->accessSummary();
	
?>

    <?= $this->Html->tag(
			'h3', 
			sprintf($outputPattern, $artistName, $managerName, $access),
			['escape' => FALSE]); ?>

<?php endforeach; ?>

    <h1>Manager Manifests</h1>

<?php foreach ($managerManifests->load() as $managerManifest) :
	$artistName = $managerManifest->artistCard()->rootDisplayValue();
    $managerName = $managerManifest->selfAssigned() 
			? 'self' 
			: $managerManifest->managerCard()->rootDisplayValue();
    $access = $managerManifest->accessSummary();
?>

    <?= $this->Html->tag(
			'h3', 
			sprintf($outputPattern, $artistName, $managerName, $access),
			['escape' => FALSE]); ?>

<?php endforeach; ?>

