<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ArtStack $artStack
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Art Stack'), ['action' => 'edit', $artStack->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Art Stack'), ['action' => 'delete', $artStack->id], ['confirm' => __('Are you sure you want to delete # {0}?', $artStack->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Art Stacks'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Art Stack'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Images'), ['controller' => 'Images', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Image'), ['controller' => 'Images', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Editions'), ['controller' => 'Editions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Edition'), ['controller' => 'Editions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="artStacks view large-9 medium-8 columns content">
    <h3><?= h($artStack->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $artStack->has('user') ? $this->Html->link($artStack->user->id, ['controller' => 'Users', 'action' => 'view', $artStack->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Image') ?></th>
            <td><?= $artStack->has('image') ? $this->Html->link($artStack->image->title, ['controller' => 'Images', 'action' => 'view', $artStack->image->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($artStack->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($artStack->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Edition Count') ?></th>
            <td><?= $this->Number->format($artStack->edition_count) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($artStack->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($artStack->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Description') ?></h4>
        <?= $this->Text->autoParagraph(h($artStack->description)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Editions') ?></h4>
        <?php if (!empty($artStack->editions)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Type') ?></th>
                <th scope="col"><?= __('Quantity') ?></th>
                <th scope="col"><?= __('Artwork Id') ?></th>
                <th scope="col"><?= __('Series Id') ?></th>
                <th scope="col"><?= __('Assigned Piece Count') ?></th>
                <th scope="col"><?= __('Format Count') ?></th>
                <th scope="col"><?= __('Fluid Piece Count') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($artStack->editions as $editions): ?>
            <tr>
                <td><?= h($editions->id) ?></td>
                <td><?= h($editions->created) ?></td>
                <td><?= h($editions->modified) ?></td>
                <td><?= h($editions->user_id) ?></td>
                <td><?= h($editions->title) ?></td>
                <td><?= h($editions->type) ?></td>
                <td><?= h($editions->quantity) ?></td>
                <td><?= h($editions->artwork_id) ?></td>
                <td><?= h($editions->series_id) ?></td>
                <td><?= h($editions->assigned_piece_count) ?></td>
                <td><?= h($editions->format_count) ?></td>
                <td><?= h($editions->fluid_piece_count) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Editions', 'action' => 'view', $editions->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Editions', 'action' => 'edit', $editions->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Editions', 'action' => 'delete', $editions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $editions->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
