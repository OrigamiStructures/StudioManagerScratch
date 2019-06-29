<?php
//osd($manifests);
osd($currentUser->username());
?>
    <h1>Supervisor Manifests</h1>

<?php $assignedToForeign = $supervisorManifests
    ->find('manifest')
    ->specifyFilter('selfAssigned', FALSE)
    ->loadStacks();
//osd($assignedToForeign);
foreach ($supervisorManifests->load() as $supervisorManifest) {
    if($supervisorManifest->selfAssigned()){
        continue;
    }
    $managerName = $supervisorManifest->managerCard()->rootDisplayValue();
    $access = $supervisorManifest->accessSummary();
    ?>

    <?= $this->Html->tag('h3', $supervisorManifest->artistCard()->rootDisplayValue() . " (to $managerName)-$access"); ?>

<?php } ?>

    <h1>Manager Manifests</h1>

<?php foreach ($managerManifests->load() as $managerManifest) {
    $self = $managerManifest->selfAssigned() ? 'self' : $managerManifest->managerCard()->rootDisplayValue();
    $access = $managerManifest->accessSummary();
    ?>


    <?= $this->Html->tag('h3', $managerManifest->artistCard()->rootDisplayValue() . " ($self)-$access" ); ?>

<?php } ?>

