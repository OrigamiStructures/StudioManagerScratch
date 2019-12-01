<h3>Needs tools to allow SuperUsers (and Supervisors?) to change ContextUser settings</h3>

<?php
//die;
use App\Model\Lib\ManifestStacksSet;

/* @var \App\View\AjaxView $this */

    $this->loadHelper('People');

    /* @var \App\Model\Lib\StackSet $manifestsIssued */
    /* @var \App\Model\Lib\ContextUser $contextUser */

//	$delegatedManagement = $manifestsIssued
//        ->getLayer('manifest')
//        ->find()
//        ->specifyFilter('manager_id', $contextUser->getId('supervisor'), '!=')
//        ->toArray();
$manifestStack = new ManifestStacksSet($manifestsIssued);

$delegatedManagement = $manifestStack
    ->delegatedManagement($contextUser->getId('supervisor'));?>

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

<?php foreach ($manifestsReceived->getData() as $agrement) : ?>

    <?= "<p>{$this->People->artistManifestSummary($agrement)}</p>"; ?>

<?php endforeach; ?>

<?php


