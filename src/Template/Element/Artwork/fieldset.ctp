<!-- Element/Artwork/fieldset.ctp -->
<fieldset>
    <?= $this->Form->input('Artwork.id'); ?>
    <?= $this->Form->input('Artwork.title', ['label' => 'Artwork Title']); ?>
    <?= $this->Form->input('Artwork.description', ['placeholder' => 'Optional artwork description', 'label' => 'Artwork Description']); ?>
</fieldset>
