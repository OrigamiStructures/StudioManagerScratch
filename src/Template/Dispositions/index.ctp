<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Disposition'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Pieces'), ['controller' => 'Pieces', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Piece'), ['controller' => 'Pieces', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="dispositions index large-9 medium-8 columns content">
    <h3><?= __('Dispositions') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('created') ?></th>
                <th><?= $this->Paginator->sort('modified') ?></th>
                <th><?= $this->Paginator->sort('user_id') ?></th>
                <th><?= $this->Paginator->sort('member_id') ?></th>
                <th><?= $this->Paginator->sort('start_date') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dispositions as $disposition): ?>
            <tr>
                <td><?= $this->Number->format($disposition->id) ?></td>
                <td><?= h($disposition->created) ?></td>
                <td><?= h($disposition->modified) ?></td>
                <td><?= $disposition->has('user') ? $this->Html->link($disposition->user->id, ['controller' => 'Users', 'action' => 'view', $disposition->user->id]) : '' ?></td>
                <td><?= $disposition->has('member') ? $this->Html->link($disposition->member->name(), ['controller' => 'Members', 'action' => 'view', $disposition->member->id]) : '' ?></td>
                <td><?= h($disposition->start_date) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $disposition->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $disposition->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $disposition->id], ['confirm' => __('Are you sure you want to delete # {0}?', $disposition->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
