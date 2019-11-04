<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Series'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Editions'), ['controller' => 'Editions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Edition'), ['controller' => 'Editions', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="series index large-9 medium-8 columns content">
    <h3><?= __('Series') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('created') ?></th>
                <th><?= $this->Paginator->sort('modified') ?></th>
                <th><?= $this->Paginator->sort('user_id') ?></th>
                <th><?= $this->Paginator->sort('title') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($series as $occurrence): ?>
            <tr>
                <td><?= $this->Number->format($occurrence->id) ?></td>
                <td><?= h($occurrence->created) ?></td>
                <td><?= h($occurrence->modified) ?></td>
                <td><?= $occurrence->has('user') ? $this->Html->link($occurrence->user->id, ['controller' => 'Users', 'action' => 'view', $occurrence->user->id]) : '' ?></td>
                <td><?= h($occurrence->title) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $occurrence->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $occurrence->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $occurrence->id], ['confirm' => __('Are you sure you want to delete # {0}?', $occurrence->id)]) ?>
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
