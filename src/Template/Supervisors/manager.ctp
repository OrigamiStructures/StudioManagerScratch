<?php
// <editor-fold defaultstate="collapsed" desc="VARIABLE SETUP">
    $baseManifest = $managerManifests->shift();
    $managerCard = $baseManifest->managerCard();
// </editor-fold>
?>

<?php
echo $this->Html->link(['controller' => 'supervisors', 'action' => 'index']);
echo $this->Element('Common/LocationBanner', ['label' => "Supervise Manager {$managerCard->name()}"]);
echo $baseManifest->selfAssigned() ? "Self-Assigned" : "Assigned By Other";
foreach ($managerManifests->load() as $manifest) {
	echo $this->People->manifestSummary($manifest);

}