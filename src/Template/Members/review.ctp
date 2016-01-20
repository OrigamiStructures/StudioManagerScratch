<!-- Template/Member/review.ctp -->
<?php
    foreach ($members as $member) {
        $this->set('member', $member);
        echo $this->element('Member/'.$element_management['member']);
    }
?>
