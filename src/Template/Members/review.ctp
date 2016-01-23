<!-- Template/Member/review.ctp -->
<?php
    if($SystemState->isKnown('member')){
        echo $this->element('Member/full');
    } else {
        echo $this->element('Member/many');
    }
?>
