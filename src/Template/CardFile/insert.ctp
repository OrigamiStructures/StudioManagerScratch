<?= $this->element('Cardfile/sidebar'); ?>
<div class="members form large-9 medium-8 columns content">
<!--    --><?//= osd($member); ?>
    <?= $this->Form->create($member) ?>
    <fieldset>
        <legend><?= __('Add Card') ?></legend>
        <?php
            echo $this->Form->input('type');
            echo $this->Form->input('name');
            echo $this->Form->input('user_id');
            echo $this->Form->input('image_id', ['options' => $images, 'empty' => true]);
            echo $this->Form->input('groups._ids', ['options' => $groups]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
