<!-- Element/Member/controls.ctp -->
    <?php
        if(!$SystemState->isKnown('member')){
            echo $this->Html->link('Review', ['action' => 'review', '?' => ['member' => $member->id]], ['class' => 'button']);
        }
        echo $this->Html->link('Refine', ['action' => 'refine', '?' => ['member' => $member->id]], ['class' => 'button']);
        echo $this->Form->postLink(__('Delete'), 
                ['controller' => 'Members', 'action' => 'delete', $member->id], 
                ['confirm' => __("Are you sure you want to delete this {$member->memberLabel('lower')}"),
                        'class' => 'button alert']
                );
//            echo $this->Form->button('Delete', ['class' => 'button alert']);
//        echo $this->Html->link('Delete', ['action' => 'delete', '?' => ['member' => $member->id]], ['class' => 'button alert', 'confirm' => 'Are you sure you want to delete this member?']);
    ?>
<!-- END Element/Member/controls.ctp -->
