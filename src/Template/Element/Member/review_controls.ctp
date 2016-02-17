<!-- Element/Member/review_controls.ctp -->
<?php
    echo $this->Html->link('Refine', [
        'action' => 'refine', 
        '?' => [
            'member' => $member->id,
            'type' => $member->member_type
            ]
        ], 
        ['class' => 'button']);
?>
<!-- END Element/Member/review_controls.ctp -->
