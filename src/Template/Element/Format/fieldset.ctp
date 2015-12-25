<!-- Element/Format/fieldset.ctp -->
<fieldset>
    <?= $this->Form->input('Format.id'); ?>
    <?= $this->Form->input('Format.edition_id', [
        'type' => 'hidden'
    ]); ?>
    <?= $this->Form->input('Format.title', ['placeholder' => 'Optional Format Title', 'label' => 'Format Title']); ?>
    <?= $this->Form->input('Format.description', ['placeholder' => 'Media, size and other format details']); ?>
    <?= $this->Form->input('Format.range_flag'); ?>
    <?= $this->Form->input('Format.range_start'); ?>
    <?= $this->Form->input('Format.range_end'); ?>
</fieldset>
