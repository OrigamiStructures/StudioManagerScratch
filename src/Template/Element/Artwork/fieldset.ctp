<!-- Element/Artwork/fieldset.ctp -->
<fieldset>
    <?= $this->Form->input('id'); ?>
    <?= $this->Form->input('title', ['label' => 'Artwork Title']); ?>
    <?= $this->Form->input('description', ['placeholder' => 'Optional artwork description', 'label' => 'Artwork Description']); ?>
    <?= $this->Form->input('image_id', 
			['type' => 'hidden']); ?>
</fieldset>
<?= $this->element('Image/artwork_fieldset'); ?>

