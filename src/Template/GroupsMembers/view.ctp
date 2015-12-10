<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Groups Member'), ['action' => 'edit', $groupsMember->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Groups Member'), ['action' => 'delete', $groupsMember->id], ['confirm' => __('Are you sure you want to delete # {0}?', $groupsMember->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Groups Members'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Groups Member'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Groups'), ['controller' => 'Groups', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Group'), ['controller' => 'Groups', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="groupsMembers view large-9 medium-8 columns content">
    <h3><?= h($groupsMember->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $groupsMember->has('user') ? $this->Html->link($groupsMember->user->id, ['controller' => 'Users', 'action' => 'view', $groupsMember->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Group') ?></th>
            <td><?= $groupsMember->has('group') ? $this->Html->link($groupsMember->group->name, ['controller' => 'Groups', 'action' => 'view', $groupsMember->group->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Member') ?></th>
            <td><?= $groupsMember->has('member') ? $this->Html->link($groupsMember->member->name, ['controller' => 'Members', 'action' => 'view', $groupsMember->member->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($groupsMember->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($groupsMember->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($groupsMember->modified) ?></td>
        </tr>
    </table>
</div>
