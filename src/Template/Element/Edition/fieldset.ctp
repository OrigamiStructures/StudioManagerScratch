<!-- Element/Edition/fieldset.ctp -->
<fieldset>
    <?= $this->Form->input('editions..id'); ?>
    <?= $this->Form->input('editions..artwork_id', [
        'type' => 'hidden'
    ]); ?>
    <?= $this->Form->input('editions..title', ['placeholder' => 'Optional Edition Title', 'label' => 'Edition Title']); ?>
    <?= $this->Form->input('editions..type', ['options' => $types]); ?>
    <?= $this->Form->input('editions..quantity', ['default' => 1]); ?>
</fieldset>
