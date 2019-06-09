<?php
//osd($results->count());
//foreach ($results->find()->setLayer('identity')->specifyFilter('last_name', 'Drake')->load() as $index => $identity) {
//    osd($identity);
//}
//osd($results
//    ->find()
//    ->setLayer('identity')
//    ->setAccessNodeObject('value', 'id')
//    ->loadValueList());
$IDs = $results->IDs();
osd($IDs);
$resultIds = $results
    ->find()
    ->setLayer('identity')
    ->setAccessNodeObject('value', 'id')
    ->loadValueList();

foreach ($IDs as $ID) {
    if($ID == 75){continue;}
    $rolodexCardEntity = $results->element($ID);
    osd($rolodexCardEntity->rootDisplayValue());
}
//osd($results->find()->setLayer('identity')->specifyFilter('last_name', 'Drake')->load());
//osd($results);
?>