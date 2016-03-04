<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $disposition->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $disposition->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Dispositions'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Addresses'), ['controller' => 'Addresses', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Address'), ['controller' => 'Addresses', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Pieces'), ['controller' => 'Pieces', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Piece'), ['controller' => 'Pieces', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="dispositions form large-9 medium-8 columns content">
    <?= $this->Form->create($disposition) ?>
    <fieldset>
        <legend><?= __('Edit Disposition') ?></legend>
        <?php
            echo $this->Form->input('user_id', ['options' => $users, 'empty' => true]);
            echo $this->Form->input('member_id', ['options' => $members, 'empty' => true]);
            echo $this->Form->input('address_id', ['options' => $addresses, 'empty' => true]);
            echo $this->Form->input('start_date', ['empty' => true]);
            echo $this->Form->input('end_date', ['empty' => true]);
            echo $this->Form->input('type');
            echo $this->Form->input('label');
            echo $this->Form->input('complete');
            echo $this->Form->input('disposition_id');
            echo $this->Form->input('name');
            echo $this->Form->input('pieces._ids', ['options' => $pieces]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
