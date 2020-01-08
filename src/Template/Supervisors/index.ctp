<p>Needs tools to allow SuperUsers (and Supervisors?) to change ContextUser settings</p>

<?php
use App\Model\Lib\ManifestStacksSet;

/* @var \App\View\AjaxView $this */

    $this->loadHelper('People');

    /* @var \App\Model\Lib\StackSet $manifestsIssued */
    /* @var \App\Model\Lib\ContextUser $contextUser */

$manifestStack = new ManifestStacksSet($manifestsIssued);

$delegatedManagement = $manifestStack
    ->delegatedManagement($contextUser->getId('supervisor'));?>

    <h1>Supervisor: <?= $contextUser->getCard('supervisor')->name() ?></h1>

	<p>Add an artist</p>
<?php
echo $this->Html->link(
    'Add Artist',
    'rolodexCards/add',
    ['class' => 'button medium']
);
?>
    <div class="add_artist_dialog"></div>
	<p>Recruit a new delegate tools here</p>

	<h2>Change a Management Agreement</h2>

    <h3>Delegated Management</h3>

<?php

    foreach ($delegatedManagement as $manifest) : ?>

	<?= "<p>{$manifest->managerCard()->name()}</p>"; ?>

<?php endforeach; ?>

    <h3>Owned Management</h3>

<?php foreach ($manifestsReceived->getData() as $agrement) : ?>

    <p><?= "{$this->People->artistManifestSummary($agrement)}"; ?></p>

<?php endforeach; ?>

<?php


