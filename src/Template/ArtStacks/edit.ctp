<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ArtStack $artStack
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $artStack->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $artStack->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Art Stacks'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Images'), ['controller' => 'Images', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Image'), ['controller' => 'Images', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Editions'), ['controller' => 'Editions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Edition'), ['controller' => 'Editions', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="artStacks form large-9 medium-8 columns content">
    <?= $this->Form->create($artStack) ?>
    <fieldset>
        <legend><?= __('Edit Art Stack') ?></legend>
        <?php
            echo $this->Form->control('user_id', ['options' => $users, 'empty' => true]);
            echo $this->Form->control('image_id', ['options' => $images, 'empty' => true]);
            echo $this->Form->control('title');
            echo $this->Form->control('description');
            echo $this->Form->control('edition_count');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
