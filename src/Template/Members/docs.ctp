<?php
/* @var \App\Model\Table\MembersTable $MembersTable */
/* @var \App\Model\Lib\Layer $memberLayer */
/* @var \App\Model\Lib\AppendIterator $it */
/* @var \App\Model\Lib\StackSet $people */

/*
 * Get a new, empty LAA
 */
$argObj = $memberLayer->getArgObj();

debug($argObj);

/*
 * Get one from an LAP
 */
$lap = $memberLayer->getLayer();
$selectList = $lap->find()->toKeyValueList('id', 'name');

debug($lap);

$argObj = $lap->cloneArgObj();

debug($argObj);

/*
 * simple
 */
$layerAccessProcessor = $memberLayer->getLayer($memberLayer);

debug($layerAccessProcessor);


$layer = layer([
    new \App\Model\Entity\Member(['id' => 1, 'first_name' => 'one']),
    new \App\Model\Entity\Member(['id' => 2, 'first_name' => 'two']),
]);
$array =[
    new \App\Model\Entity\Member(['id' => 3, 'first_name' => 'three']),
    new \App\Model\Entity\Member(['id' => 4, 'first_name' => 'four']),
];
$entity = new \App\Model\Entity\Member(['id' => 5, 'first_name' => 'five']);
/*
 * fully manual
 *
 * All the entities inserted must be of the same type
 * and they must all match the type passed to the constructor
 *
 * @param $entityType string lower case singular version of the Entity class
 */
$lap = new \App\Model\Lib\LayerAccessProcessor('member', 'Member');
$lap->insert($layer)        //you can chain the insert calls
    ->insert($array)
    ->insert($entity);

debug($lap);


$memberProcessor = $memberLayer->getLayer();
$groupFilters = [
    'People' => $memberLayer->getArgObj()     //you can chain off the accessor if you want
        ->specifyFilter('member_type', 'Person'),
    'Institutions' => $memberLayer->getArgObj()
        ->specifyFilter('member_type', MEMBER_TYPE_ORGANIZATION)
];

$result = [];
foreach($groupFilters as $type => $filter) {
    $filter->specifyPagination(1, 3);               //you can keep modifying the LAA
    $memberProcessor->setArgObj($filter);           //and use it when you're ready
    $result[$type] = $memberProcessor->toArray();
}

debug($result);

debug(count($memberLayer));
debug($memberLayer
    ->getLayer()
    ->find()
    ->specifyFilter('member_type', 'Category', '!=')
    ->specifySort('last_name', SORT_ASC, SORT_STRING)
    ->specifyPagination(2, 10)
    ->toKeyValueList('name', 'member_type')
);

//$contactEmails = $people->getLayer('contacts')
//    ->find()
//    ->specifyFilter('label', 'email')
//    ->specifySort('data', SORT_DESC)
//    ->toArray();
//
//osd($contactEmails);
osd($people->getLayer('addresses')->toDistinctList('id'));

$idents = $people->getLayer('identity')
    ->toKeyValueList('last_name', 'name');

osd($idents);

$result = ($people->getLayer('identity'))
    ->find()
    ->specifyPagination(1, 5)
    ->toLayer();

$contactPhones = $people->getLayer('contacts')
    ->find()
    ->specifyFilter('label', 'phone')
    ->specifySort('data', SORT_DESC)
    ->toDistinctList('data');

//$contactPhones = $contactPhones
//    ->getLayer()
//    ->find()
//    ->specifyFilter('data', '', '!==')
//    ->toKeyValueList('id','data');

osd($contactPhones);

//while ($it->valid()) {
//    echo "<p>{$it->getIteratorIndex()}</p>";
//    osd($it->key(), 'key');
//    osd($it->current(), 'current');
//    $it->next();
//}

//$contacts = $person->getLayer('contacts')->getAppendIterator();
//while ($contacts->valid()) {
//    echo "<h3>{$contacts->getIteratorIndex()}</h3>";
////    echo sprintf(
////        '<p>Short list %s-%s: %s: %s</p>',
////        $contacts->key(),
////        $contacts->current()->id,
////        $contacts->current()->label,
////        $contacts->current()->data
////    );
//    osd($contacts->current());
//    $contacts->next();
//}

//echo '<h1>Change</h1>';
//
//for ($i = 0; $i < 8; $i++) {
//    $person = $people->element($i);
//    $contacts = $person->getLayer('contacts')->getAppendIterator();
//    while ($contacts->valid()) {
//        echo "<h3>{$contacts->getIteratorIndex()}</h3>";
//        echo sprintf(
//            '<p>Short list %s-%s: %s: %s</p>',
//            $contacts->key(),
//            $contacts->current()->id,
//            $contacts->current()->label,
//            $contacts->current()->data
//        );
//        $contacts->next();
//    }
//}

echo '<h1>Change</h1>';

$args = $people->getLayer('contacts')->find();

$contactAccess = $people->getLayer('contacts');

$args = new \App\Model\Lib\LayerAccessArgs();

$args->specifyFilter('label', 'email');
$args->specifySort('data', SORT_ASC);
$args->specifyPagination(1, 10);

$contacts = new ArrayIterator($contactAccess->perform($args));

while ($contacts->valid()) {
    $personCards = $people->ownerOf('identity', $contacts->current()->id);
    $card = array_shift($personCards);
    if (is_a($card, '\App\Model\Entity\PersonCard')
        && is_a($card->identity, '\App\Model\Lib\Layer')) {
        $identity = $card->rootElement();
        $name = $identity->name();
    } else {
        $name = 'unknown';
    }
    echo '<p>' . $name . ' : ' . $contacts->current()->asString() . '</p>';
    $contacts->next();
}

