<?php
/* @var \App\Model\Table\MembersTable $MembersTable */
/* @var \App\Model\Lib\Layer $memberLayer */
/* @var \App\Model\Lib\AppendIterator $it */


while ($it->valid()) {
    echo "<p>{$it->getIteratorIndex()}</p>";
    osd($it->key(), 'key');
    osd($it->current(), 'current');
    $it->next();
}


