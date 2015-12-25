<!-- Element/Edition/fieldset.ctp -->
<fieldset>
    <?= $this->Form->input('Edition.id'); ?>
    <?= $this->Form->input('Edition.artwork_id', [
        'type' => 'hidden'
    ]); ?>
    <?= $this->Form->input('Edition.title', ['placeholder' => 'Optional Edition Title', 'label' => 'Edition Title']); ?>
    <?= $this->Form->input('Edition.type', ['options' => $types]); ?>
    <?= $this->Form->input('Edition.quantity', ['default' => 1]); ?>
</fieldset>
