<p>DelegatedManagement</p>
<p>OwnedManagement</p>
<?php

    $this->loadHelper('People');
    //osd($manifests);
//    osd($currentUser->username());

    $assignedToForeign = $managerManifests
        ->find('manifest')
        ->specifyFilter('selfAssigned', FALSE)
        ->loadStacks();
		
?>
	<h1>Add an artist</h1>
	<p>tools here</p>
	<h1>Recruit a new delegate</h1>
	<p>tools here</p>

	
	<h1>Change a Management Agreement</h1>
	
    <h2>Delegated Management</h2>

<?php

    foreach ($assignedToForeign as $supervisorManifest) : ?>

	<?= "<p>{$supervisorManifest->managerCard()->name()}</p>"; ?>

<?php endforeach; ?>
    <h2>Owned Management</h2>

<?php foreach ($artistManifests->load() as $managerManifest) : ?>

    <?= "<p>{$this->People->artistManifestSummary($managerManifest)}</p>"; ?>

<?php endforeach; ?>
	
<?php
	

