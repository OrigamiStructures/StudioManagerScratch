<?php
/* @var \App\Model\Table\MembersTable $MembersTable */
echo 'debug($memberLayer->linkedTo(\'user\', \'708cfc57-1162-4c5b-9092-42c25da131a9\'));';
echo "<pre>\n";
debug($memberLayer->linkedTo('user', '708cfc57-1162-4c5b-9092-42c25da131a9'));
debug($memberLayer->linkedTo('user_id', '708cfc57-1162-4c5b-9092-42c25da131a9'));
//debug($sample);
//debug($sample->load());
//debug($sample->shift());

echo "</pre>\n";
debug($members);
echo "<pre>[\n";
foreach ($members as $index => $member) {
    echo " $index => " . get_class($member) . " { id = $member->id }\n";
}
echo "]</pre>\n";
debug($memberLayer);
echo "<pre>[\n";
foreach ($memberLayer->load() as $index => $member) {
    echo " $index => " . get_class($member) . " { id = $member->id }\n";
}
echo "]</pre>\n";
