<?php
/* @var \App\Model\Table\MembersTable $MembersTable */
/* @var \App\Model\Lib\Layer $memberLayer */
//echo 'debug($memberLayer->sort(\'last_name\', \SORT_ASC, \SORT_NATURAL));';
//echo "<pre>\n";
osd(
    $memberLayer
        ->find()
        ->specifyFilter('member_type', 'Person')
        ->loadDistinct('user_id')
);

osd(
$memberLayer
    ->find()
    ->specifyFilter('member_type', 'Category', '!==')
    ->loadKeyValueList('id', 'name')
);

$filtered = layer($memberLayer
    ->find()
    ->specifyFilter('member_type', 'Person')
    ->load()
);
osd(
    layer($filtered ->sort('last_name', SORT_ASC, SORT_STRING))

);


//$findByType = new \App\Model\Lib\LayerAccessArgs();
//
//$findByType->setFilterOperator('===');
//$findByType->setFilterTestSubject('member_type');
//
//foreach (['Person', 'Category', 'Unknown'] as $type) {
//    ;
//    debug($memberLayer->load($findByType->filterValue($type)));
//}


//debug($sample);
//debug($sample->load());
//debug($sample->shift());

//echo "</pre>\n";
//debug($members);
//echo "<pre>[\n";
//foreach ($members as $index => $member) {
//    echo " $index => " . get_class($member) . " { id = $member->id }\n";
//}
//echo "]</pre>\n";
//debug($memberLayer);
//echo "<pre>[\n";
//foreach ($memberLayer->load() as $index => $member) {
//    echo " $index => " . get_class($member) . " { id = $member->id }\n";
//}
//echo "]</pre>\n";
