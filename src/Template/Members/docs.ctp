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
osd($args);
$args
    ->setLayer('contacts')
    ->specifyFilter('label', 'phone');
osd($args);

$contacts = new ArrayIterator($contactAccess->perform($args));

//debug($contacts);

while ($contacts->valid()) {
//    echo "<h3>{$contacts->getIteratorIndex()}</h3>";
        echo '<p>' . $contacts->current()->asString() . '</p>';
    $contacts->next();
}

