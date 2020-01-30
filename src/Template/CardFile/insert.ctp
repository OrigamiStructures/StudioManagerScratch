<?= $this->element('Cardfile/sidebar'); ?>
<div class="members form large-9 medium-8 columns content">
    <?= $this->Form->create($cardfile) ?>
<!--    --><?//= osd($cardfile); ?>
    <fieldset>
        <legend><?= __('Add Card') ?></legend>
        <?php
            echo $this->Form->select('type', MEMBER_TYPE_ARRAY);
            echo $this->Form->control('isArtist', ['type' => 'checkbox', 'label' => ' Artist']);
            echo $this->Form->input('name');
            echo $this->Form->input('user_id');
            echo $this->Form->input('image_id', ['options' => $images, 'empty' => true]);
            echo $this->Form->input('groups._ids', ['options' => $groups]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
