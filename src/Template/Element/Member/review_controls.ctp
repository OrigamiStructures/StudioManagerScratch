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
		if ($standing_disposition) {
        echo $this->Html->link('Dispose', [
			'controller' => 'dispositions',
            'action' => 'create', 
            '?' => [
                'member' => $member->id,
                ]
            ], 
            ['class' => 'button']);
		}
?>
<!-- END Element/Member/review_controls.ctp -->
