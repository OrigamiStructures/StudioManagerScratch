<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Piece'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Editions'), ['controller' => 'Editions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Edition'), ['controller' => 'Editions', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Formats'), ['controller' => 'Formats', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Format'), ['controller' => 'Formats', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Dispositions'), ['controller' => 'Dispositions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Disposition'), ['controller' => 'Dispositions', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="pieces index large-9 medium-8 columns content">
    <h3><?= __('Pieces') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('created') ?></th>
                <th><?= $this->Paginator->sort('modified') ?></th>
                <th><?= $this->Paginator->sort('user_id') ?></th>
                <th><?= $this->Paginator->sort('edition_id') ?></th>
                <th><?= $this->Paginator->sort('format_id') ?></th>
                <th><?= $this->Paginator->sort('number') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pieces as $piece): ?>
            <tr>
                <td><?= $this->Number->format($piece->id) ?></td>
                <td><?= h($piece->created) ?></td>
                <td><?= h($piece->modified) ?></td>
                <td><?= $piece->has('user') ? $this->Html->link($piece->user->id, ['controller' => 'Users', 'action' => 'view', $piece->user->id]) : '' ?></td>
                <td><?= $piece->has('edition') ? $this->Html->link($piece->edition->name, ['controller' => 'Editions', 'action' => 'view', $piece->edition->id]) : '' ?></td>
                <td><?= $piece->has('format') ? $this->Html->link($piece->format->title, ['controller' => 'Formats', 'action' => 'view', $piece->format->id]) : '' ?></td>
                <td><?= $this->Number->format($piece->number) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $piece->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $piece->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $piece->id], ['confirm' => __('Are you sure you want to delete # {0}?', $piece->id)]) ?>
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
