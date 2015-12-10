<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Groups Member'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Groups'), ['controller' => 'Groups', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Group'), ['controller' => 'Groups', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="groupsMembers index large-9 medium-8 columns content">
    <h3><?= __('Groups Members') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('created') ?></th>
                <th><?= $this->Paginator->sort('modified') ?></th>
                <th><?= $this->Paginator->sort('user_id') ?></th>
                <th><?= $this->Paginator->sort('group_id') ?></th>
                <th><?= $this->Paginator->sort('member_id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groupsMembers as $groupsMember): ?>
            <tr>
                <td><?= $this->Number->format($groupsMember->id) ?></td>
                <td><?= h($groupsMember->created) ?></td>
                <td><?= h($groupsMember->modified) ?></td>
                <td><?= $groupsMember->has('user') ? $this->Html->link($groupsMember->user->id, ['controller' => 'Users', 'action' => 'view', $groupsMember->user->id]) : '' ?></td>
                <td><?= $groupsMember->has('group') ? $this->Html->link($groupsMember->group->name, ['controller' => 'Groups', 'action' => 'view', $groupsMember->group->id]) : '' ?></td>
                <td><?= $groupsMember->has('member') ? $this->Html->link($groupsMember->member->name, ['controller' => 'Members', 'action' => 'view', $groupsMember->member->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $groupsMember->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $groupsMember->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $groupsMember->id], ['confirm' => __('Are you sure you want to delete # {0}?', $groupsMember->id)]) ?>
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
