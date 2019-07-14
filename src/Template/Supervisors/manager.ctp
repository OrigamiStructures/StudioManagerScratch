<?php
osd($managerManifests->count());
//debug($managerManifests->load());//die;
foreach ($managerManifests->load() as $manifest) {
//	debug(get_class($manifest));
	echo $this->People->manifestSummary($manifest); 

}