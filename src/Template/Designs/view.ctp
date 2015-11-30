<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Design'), ['action' => 'edit', $design->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Design'), ['action' => 'delete', $design->id], ['confirm' => __('Are you sure you want to delete # {0}?', $design->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Designs'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Design'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="designs view large-9 medium-8 columns content">
    <h3><?= h($design->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($design->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($design->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($design->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Content') ?></h4>
        <?= $this->Text->autoParagraph(h($design->content)); ?>
    </div>
</div>
