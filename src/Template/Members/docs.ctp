<?php
/* @var \App\Model\Table\MembersTable $MembersTable */
/* @var \App\Model\Lib\Layer $memberLayer */
/* @var \App\Model\Lib\AppendIterator $it */
/* @var \App\Model\Lib\StackSet $people */


//while ($it->valid()) {
//    echo "<p>{$it->getIteratorIndex()}</p>";
//    osd($it->key(), 'key');
//    osd($it->current(), 'current');
//    $it->next();
//}

//$person = $people->shift();
//$contacts = $person->getLayer('contacts')->getAppendIterator();
//while ($contacts->valid()) {
//    echo "<h3>{$contacts->getIteratorIndex()}</h3>";
//    echo sprintf(
//        '<p>Short list %s-%s: %s: %s</p>',
//        $contacts->key(),
//        $contacts->current()->id,
//        $contacts->current()->label,
//        $contacts->current()->data
//    );
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


$contactAccess = $people->getLayer('contacts');

$args = new \App\Model\Lib\LayerAccessArgs();

$args->specifyFilter('label', 'email');
//$args->specifyFilter('label', ['phone', 'email'], '!in_array');
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

