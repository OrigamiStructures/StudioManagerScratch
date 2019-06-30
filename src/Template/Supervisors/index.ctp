<?php
//osd($manifests);
osd($currentUser->username());

$assignedToForeign = $supervisorManifests
    ->find('manifest')
    ->specifyFilter('selfAssigned', FALSE)
    ->loadStacks()

?>
    <h1>Supervisor Manifests</h1>

<?php ;
foreach ($assignedToForeign as $supervisorManifest) :
	$artistName = $supervisorManifest->artistCard()->rootDisplayValue();
    $managerName = $supervisorManifest->managerCard()->rootDisplayValue();
    $access = $supervisorManifest->accessSummary();
?>

    <?= $this->Html->tag('h3', "Artist: $artistName (Manager: $managerName has $access)"); ?>

<?php endforeach; ?>

    <h1>Manager Manifests</h1>

<?php foreach ($managerManifests->load() as $managerManifest) :
	$artistName = $managerManifest->artistCard()->rootDisplayValue();
    $managerName = $managerManifest->selfAssigned() 
			? 'self' 
			: $managerManifest->managerCard()->rootDisplayValue();
    $access = $managerManifest->accessSummary();
?>

    <?= $this->Html->tag('h3', "Aritist: $artistName (Manager: $managerName has $access)" ); ?>

<?php endforeach; ?>

