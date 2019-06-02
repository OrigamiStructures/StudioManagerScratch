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
$resultIds = $results
    ->find()
    ->setLayer('identity')
    ->setAccessNodeObject('value', 'id')
    ->loadValueList();

foreach ($resultIds as $resultId) {
    $rolodexCardEntity = $results->element($resultId);
    osd($rolodexCardEntity->rootDisplayValue());
}
osd($results->find()->setLayer('identity')->specifyFilter('last_name', 'Drake')->load());
//osd($results);
?>