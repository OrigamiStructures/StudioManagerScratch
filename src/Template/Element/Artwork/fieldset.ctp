<!-- Element/Artwork/fieldset.ctp -->
<fieldset>
    <?= $this->Form->input('id'); ?>
    <?= $this->Form->input('title', ['label' => 'Artwork Title']); ?>
    <?= $this->Form->input('description', ['placeholder' => 'Optional artwork description', 'label' => 'Artwork Description']); ?>
</fieldset>