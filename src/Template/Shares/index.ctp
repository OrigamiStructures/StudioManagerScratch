<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Share[]|\Cake\Collection\CollectionInterface $shares
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Share'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="shares index large-9 medium-8 columns content">
    <h3><?= __('Shares') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('supervisor_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('manager_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('category_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($shares as $share): ?>
            <tr>
                <td><?= $this->Number->format($share->id) ?></td>
                <td><?= h($share->created) ?></td>
                <td><?= h($share->modified) ?></td>
                <td><?= $this->Number->format($share->supervisor_id) ?></td>
                <td><?= $this->Number->format($share->manager_id) ?></td>
                <td><?= $this->Number->format($share->category_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $share->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $share->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $share->id], ['confirm' => __('Are you sure you want to delete # {0}?', $share->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
