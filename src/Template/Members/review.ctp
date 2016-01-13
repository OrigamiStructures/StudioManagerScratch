<!-- Template/Member/review.ctp -->
<?php
osd($member, 'member');
osd($members, 'members');
osd($element_management, 'element_management');
die;
?>
<?= $this->element('Member/'.$element_management['member']);?>