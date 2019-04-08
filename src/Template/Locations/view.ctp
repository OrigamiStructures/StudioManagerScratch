<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Location'), ['action' => 'edit', $location->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Location'), ['action' => 'delete', $location->id], ['confirm' => __('Are you sure you want to delete # {0}?', $location->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Locations'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Location'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Dispositions'), ['controller' => 'Dispositions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Disposition'), ['controller' => 'Dispositions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="locations view large-9 medium-8 columns content">
    <h3><?= h($location->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $location->has('user') ? $this->Html->link($location->user->id, ['controller' => 'Users', 'action' => 'view', $location->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Member') ?></th>
            <td><?= $location->has('member') ? $this->Html->link($location->member->name(), ['controller' => 'Members', 'action' => 'view', $location->member->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Name') ?></th>
            <td><?= h($location->name) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($location->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($location->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($location->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Dispositions') ?></h4>
        <?php if (!empty($location->dispositions)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Member Id') ?></th>
                <th><?= __('Location Id') ?></th>
                <th><?= __('Piece Id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($location->dispositions as $dispositions): ?>
            <tr>
                <td><?= h($dispositions->id) ?></td>
                <td><?= h($dispositions->created) ?></td>
                <td><?= h($dispositions->modified) ?></td>
                <td><?= h($dispositions->user_id) ?></td>
                <td><?= h($dispositions->member_id) ?></td>
                <td><?= h($dispositions->location_id) ?></td>
                <td><?= h($dispositions->piece_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Dispositions', 'action' => 'view', $dispositions->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Dispositions', 'action' => 'edit', $dispositions->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Dispositions', 'action' => 'delete', $dispositions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $dispositions->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
