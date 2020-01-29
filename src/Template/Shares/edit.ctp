<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Share $share
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $share->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $share->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Shares'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="shares form large-9 medium-8 columns content">
    <?= $this->Form->create($share) ?>
    <fieldset>
        <legend><?= __('Edit Share') ?></legend>
        <?php
            echo $this->Form->control('supervisor_id');
            echo $this->Form->control('manager_id');
            echo $this->Form->control('category_id');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
