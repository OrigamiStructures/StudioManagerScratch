<!-- Element/Member/controls.ctp -->
    <?php if(!$SystemState->isKnown('member')) : ?>
            <?= $this->Html->link('Review', [
                'action' => 'review', 
                '?' => [
                    'member' => $member->id,
                    'type' => $member->member_type
                    ]
                ]/*, 
                ['class' => 'button']*/); ?>
 : 
    <?php endif; ?>
        <?= $this->Html->link('Refine', [
            'action' => 'refine', 
            '?' => [
                'member' => $member->id,
                'type' => $member->member_type
                ]
                ]/*, 
                ['class' => 'button']*/); ?>
 :        
        <?= $this->Form->postLink(__('Delete'), 
                ['controller' => 'Members', 'action' => 'delete', $member->id], 
                ['confirm' => __("Are you sure you want to delete this {$member->memberLabel('lower')}"),
                        /*'class' => 'button alert'*/]
                ); ?>
	<?php if ($standing_disposition) : ?>
 : 
        <?= $this->Html->link('Dispose', [
			'controller' => 'dispositions',
            'action' => 'create', 
            '?' => [
                'member' => $member->id,
                ]
                ]/*, 
                ['class' => 'button']*/); ?>
	<?php endif; ?>

<!-- END Element/Member/controls.ctp -->
