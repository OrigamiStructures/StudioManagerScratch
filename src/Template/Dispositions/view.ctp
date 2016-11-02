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
            <th><?= __('Type') ?></th>
            <td><?= h($disposition->type) ?></td>
        </tr>
        <tr>
            <th><?= __('Label') ?></th>
            <td><?= h($disposition->label) ?></td>
        </tr>
        <tr>
            <th><?= __('Name') ?></th>
            <td><?= h($disposition->name) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($disposition->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Disposition Id') ?></th>
            <td><?= $this->Number->format($disposition->disposition_id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($disposition->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($disposition->modified) ?></td>
        </tr>
        <tr>
            <th><?= __('Start Date') ?></th>
            <td><?= h($disposition->start_date) ?></td>
        </tr>
        <tr>
            <th><?= __('End Date') ?></th>
            <td><?= h($disposition->end_date) ?></td>
        </tr>
        <tr>
            <th><?= __('Complete') ?></th>
            <td><?= $disposition->complete ? __('Yes') : __('No'); ?></td>
         </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Pieces') ?></h4>
        <?php if (!empty($disposition->pieces)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Number') ?></th>
                <th><?= __('Quantity') ?></th>
                <th><?= __('Made') ?></th>
                <th><?= __('Edition Id') ?></th>
                <th><?= __('Format Id') ?></th>
                <th><?= __('Disposition Count') ?></th>
                <th><?= __('Collected') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($disposition->pieces as $pieces): ?>
            <tr>
                <td><?= h($pieces->id) ?></td>
                <td><?= h($pieces->created) ?></td>
                <td><?= h($pieces->modified) ?></td>
                <td><?= h($pieces->user_id) ?></td>
                <td><?= h($pieces->number) ?></td>
                <td><?= h($pieces->quantity) ?></td>
                <td><?= h($pieces->made) ?></td>
                <td><?= h($pieces->edition_id) ?></td>
                <td><?= h($pieces->format_id) ?></td>
                <td><?= h($pieces->disposition_count) ?></td>
                <td><?= h($pieces->collected) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Pieces', 'action' => 'view', $pieces->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Pieces', 'action' => 'edit', $pieces->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Pieces', 'action' => 'delete', $pieces->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pieces->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
