<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Series'), ['action' => 'edit', $series->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Series'), ['action' => 'delete', $series->id], ['confirm' => __('Are you sure you want to delete # {0}?', $series->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Series'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Series'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Editions'), ['controller' => 'Editions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Edition'), ['controller' => 'Editions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="series view large-9 medium-8 columns content">
    <h3><?= h($series->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $series->has('user') ? $this->Html->link($series->user->id, ['controller' => 'Users', 'action' => 'view', $series->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Title') ?></th>
            <td><?= h($series->title) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($series->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($series->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($series->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Description') ?></h4>
        <?= $this->Text->autoParagraph(h($series->description)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Editions') ?></h4>
        <?php if (!empty($series->editions)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('Type') ?></th>
                <th><?= __('Quantity') ?></th>
                <th><?= __('Artwork Id') ?></th>
                <th><?= __('Series Id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($series->editions as $editions): ?>
            <tr>
                <td><?= h($editions->id) ?></td>
                <td><?= h($editions->created) ?></td>
                <td><?= h($editions->modified) ?></td>
                <td><?= h($editions->user_id) ?></td>
                <td><?= h($editions->title) ?></td>
                <td><?= h($editions->type) ?></td>
                <td><?= h($editions->quantity) ?></td>
                <td><?= h($editions->artwork_id) ?></td>
                <td><?= h($editions->series_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Editions', 'action' => 'view', $editions->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Editions', 'action' => 'edit', $editions->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Editions', 'action' => 'delete', $editions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $editions->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
