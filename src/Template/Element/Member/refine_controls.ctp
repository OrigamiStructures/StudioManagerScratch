<!-- Element/Member/refine_controls.ctp -->
    <?php
        echo $this->Form->button('Submit', ['type' => 'submit', 'class' => 'button success']);
        echo $this->Html->link('Cancel', $this->request->referer(), ['class' => 'button secondary', 'confirm' => 'Are you sure you want to cancel?']);
    ?>
<!-- END Element/Member/refine_controls.ctp -->
