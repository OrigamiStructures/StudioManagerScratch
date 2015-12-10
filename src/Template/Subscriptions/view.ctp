<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Subscription'), ['action' => 'edit', $subscription->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Subscription'), ['action' => 'delete', $subscription->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subscription->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Subscriptions'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Subscription'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Formats'), ['controller' => 'Formats', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Format'), ['controller' => 'Formats', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="subscriptions view large-9 medium-8 columns content">
    <h3><?= h($subscription->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $subscription->has('user') ? $this->Html->link($subscription->user->id, ['controller' => 'Users', 'action' => 'view', $subscription->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Title') ?></th>
            <td><?= h($subscription->title) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($subscription->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Range Flag') ?></th>
            <td><?= $this->Number->format($subscription->range_flag) ?></td>
        </tr>
        <tr>
            <th><?= __('Range Start') ?></th>
            <td><?= $this->Number->format($subscription->range_start) ?></td>
        </tr>
        <tr>
            <th><?= __('Range End') ?></th>
            <td><?= $this->Number->format($subscription->range_end) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($subscription->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($subscription->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Description') ?></h4>
        <?= $this->Text->autoParagraph(h($subscription->description)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Formats') ?></h4>
        <?php if (!empty($subscription->formats)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('Description') ?></th>
                <th><?= __('Range Flag') ?></th>
                <th><?= __('Range Start') ?></th>
                <th><?= __('Range End') ?></th>
                <th><?= __('Image Id') ?></th>
                <th><?= __('Edition Id') ?></th>
                <th><?= __('Subscription Id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($subscription->formats as $formats): ?>
            <tr>
                <td><?= h($formats->id) ?></td>
                <td><?= h($formats->created) ?></td>
                <td><?= h($formats->modified) ?></td>
                <td><?= h($formats->user_id) ?></td>
                <td><?= h($formats->title) ?></td>
                <td><?= h($formats->description) ?></td>
                <td><?= h($formats->range_flag) ?></td>
                <td><?= h($formats->range_start) ?></td>
                <td><?= h($formats->range_end) ?></td>
                <td><?= h($formats->image_id) ?></td>
                <td><?= h($formats->edition_id) ?></td>
                <td><?= h($formats->subscription_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Formats', 'action' => 'view', $formats->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Formats', 'action' => 'edit', $formats->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Formats', 'action' => 'delete', $formats->id], ['confirm' => __('Are you sure you want to delete # {0}?', $formats->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
