<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Disposition'), ['action' => 'edit', $disposition->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Disposition'), ['action' => 'delete', $disposition->id], ['confirm' => __('Are you sure you want to delete # {0}?', $disposition->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Dispositions'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Disposition'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Locations'), ['controller' => 'Locations', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Location'), ['controller' => 'Locations', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Pieces'), ['controller' => 'Pieces', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Piece'), ['controller' => 'Pieces', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="dispositions view large-9 medium-8 columns content">
    <h3><?= h($disposition->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $disposition->has('user') ? $this->Html->link($disposition->user->id, ['controller' => 'Users', 'action' => 'view', $disposition->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Member') ?></th>
            <td><?= $disposition->has('member') ? $this->Html->link($disposition->member->name, ['controller' => 'Members', 'action' => 'view', $disposition->member->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Location') ?></th>
            <td><?= $disposition->has('location') ? $this->Html->link($disposition->location->name, ['controller' => 'Locations', 'action' => 'view', $disposition->location->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Piece') ?></th>
            <td><?= $disposition->has('piece') ? $this->Html->link($disposition->piece->id, ['controller' => 'Pieces', 'action' => 'view', $disposition->piece->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($disposition->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($disposition->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($disposition->modified) ?></td>
        </tr>
    </table>
</div>
