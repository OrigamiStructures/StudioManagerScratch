<!-- Element/Member/controls.ctp -->
    <?php
        if(!$SystemState->isKnown('member')){
            echo $this->Html->link('Review', ['action' => 'review', '?' => ['member' => $member->id]], ['class' => 'button']);
        }
        echo $this->Html->link('Refine', ['action' => 'refine', '?' => ['member' => $member->id]], ['class' => 'button']);
        echo $this->Form->create('member', ['action' => 'delete', 'id' => $member->id]);
            echo $this->Form->input('id', ['value' => $member->id, 'type' => 'hidden']);
            echo $this->Form->button('Delete', ['class' => 'button alert']);
//        echo $this->Html->link('Delete', ['action' => 'delete', '?' => ['member' => $member->id]], ['class' => 'button alert', 'confirm' => 'Are you sure you want to delete this member?']);
        echo $this->Form->end();
    ?>
<!-- END Element/Member/controls.ctp -->
