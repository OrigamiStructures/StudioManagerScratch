<?php
osd($results->count());
osd(count($results->find()->specifyFilter('memberships', 'drake')->load()));
osd($results->all()[1]->rootDisplayValue());
osd($results->all()[1]->rootDisplaySource());
osd($results->all()[1]->load());
osd($results);
?>