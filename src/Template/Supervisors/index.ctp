<?php
/**
 * @property \App\View\Helper\PeopleHelper $People
 */
    $this->loadHelper('People');

	$delegatedManagement = $managerManifests
			->delegatedManagement($contextUser->getId('supervisor'));
?>
	<h1>Add an artist</h1>
	<p>tools here</p>
	<h1>Recruit a new delegate</h1>
	<p>tools here</p>


	<h1>Change a Management Agreement</h1>

    <h2>Delegated Management</h2>

<?php

    foreach ($delegatedManagement as $manifest) : ?>

	<?= "<p>{$manifest->managerCard()->name()}</p>"; ?>

<?php endforeach; ?>

    <h2>Owned Management</h2>

<?php foreach ($managementAgreements->getData() as $agrement) : ?>
    <?php osd($agrement); ?>

    <?= "<p>{$this->People->artistManifestSummary($agrement)}</p>"; ?>

<?php endforeach; ?>

<?php


