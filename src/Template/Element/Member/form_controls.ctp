<!-- Element/Member/form_controls.ctp -->
    <?php
        echo $this->Form->button('Submit', ['type' => 'submit', 'class' => 'button success']);
        echo $this->Html->link('Cancel', $SystemState->referer(), ['class' => 'button secondary', 'confirm' => 'Are you sure you want to cancel?']);
    ?>
<!-- END Element/Member/form_controls.ctp -->
