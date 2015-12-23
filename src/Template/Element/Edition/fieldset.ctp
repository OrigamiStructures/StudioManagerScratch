<!-- Element/Edition/fieldset.ctp -->
<fieldset>
    <?= $this->Form->input('Edition.id'); ?>
    <?= $this->Form->input('Edition.artwork_id', [
        'type' => 'hidden'
    ]); ?>
    <?= $this->Form->input('Edition.title'); ?>
    <?= $this->Form->input('Edition.type'); ?>
    <?= $this->Form->input('Edition.quantity'); ?>
</fieldset>
