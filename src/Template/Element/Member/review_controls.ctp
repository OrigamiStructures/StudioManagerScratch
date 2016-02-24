		<!-- Element/Member/review_controls.ctp -->
		<?= $this->Html->link('Refine', [
        'action' => 'refine', 
        '?' => [
            'member' => $member->id,
            'type' => $member->member_type
            ]
        ], 
        ['class' => 'button']); ?>
	
		<?= $standing_disposition ? $this->DispositionTools->connect($member) : ''; ?>
		<!-- END Element/Member/review_controls.ctp -->
