<!-- Element/Format/fieldset.ctp -->
<fieldset>
    <?= $this->Form->input('editions..formats..id'); ?>
    <?= $this->Form->input('editions..formats..edition_id', [
        'type' => 'hidden'
    ]); ?>
    <?= $this->Form->input('editions..formats..title', ['placeholder' => 'Optional Format Title', 'label' => 'Format Title']); ?>
    <?= $this->Form->input('editions..formats..description', ['placeholder' => 'Media, size and other format details']); ?>
    <?= $this->Form->input('editions..formats..range_flag'); ?>
    <?= $this->Form->input('editions..formats..range_start'); ?>
    <?= $this->Form->input('editions..formats..range_end'); ?>
</fieldset>
