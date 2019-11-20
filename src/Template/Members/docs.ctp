<?php
/* @var \App\Model\Table\MembersTable $MembersTable */
/* @var \App\Model\Lib\Layer $memberLayer */
/* @var \App\Model\Lib\AppendIterator $it */
/* @var \App\Model\Lib\StackSet $people */

debug(count($memberLayer));
debug($memberLayer
    ->getLayer()
    ->NEWfind()
    ->specifyFilter('member_type', 'Category', '!=')
    ->specifySort('last_name', SORT_ASC, SORT_STRING)
    ->setPagination(2, 10)
    ->toKeyValueList('name', 'member_type')
);

//$contactEmails = $people->getLayer('contacts')
//    ->NEWfind()
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
    ->NEWfind()
    ->setPagination(1, 5)
    ->toLayer();

$contactPhones = $people->getLayer('contacts')
    ->NEWfind()
    ->specifyFilter('label', 'phone')
    ->specifySort('data', SORT_DESC)
    ->toDistinctList('data');

//$contactPhones = $contactPhones
//    ->getLayer()
//    ->NEWfind()
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

$args = $people->getLayer('contacts')->NEWfind();

$contactAccess = $people->getLayer('contacts');

$args = new \App\Model\Lib\LayerAccessArgs();

$args->specifyFilter('label', 'email');
$args->specifySort('data', SORT_ASC);
$args->setPagination(1, 10);

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

