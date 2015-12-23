<!-- Element/Format/fieldset.ctp -->
<fieldset>
    <?= $this->Form->input('Format.id'); ?>
    <?= $this->Form->input('Format.edition_id', [
        'type' => 'hidden'
    ]); ?>
    <?= $this->Form->input('Format.title'); ?>
    <?= $this->Form->input('Format.description'); ?>
    <?= $this->Form->input('Format.range_flag'); ?>
    <?= $this->Form->input('Format.range_start'); ?>
    <?= $this->Form->input('Format.range_end'); ?>
</fieldset>
