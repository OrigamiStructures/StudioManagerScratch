<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $edition->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $edition->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Editions'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Artworks'), ['controller' => 'Artworks', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Artwork'), ['controller' => 'Artworks', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Series'), ['controller' => 'Series', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Series'), ['controller' => 'Series', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Formats'), ['controller' => 'Formats', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Format'), ['controller' => 'Formats', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Pieces'), ['controller' => 'Pieces', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Piece'), ['controller' => 'Pieces', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="editions form large-9 medium-8 columns content">
    <?= $this->Form->create($edition) ?>
    <fieldset>
        <legend><?= __('Edit Edition') ?></legend>
        <?php
            echo $this->Form->input('user_id', ['options' => $users, 'empty' => true]);
            echo $this->Form->input('title');
            echo $this->Form->input('type');
            echo $this->Form->input('quantity');
            echo $this->Form->input('artwork_id', ['options' => $artworks, 'empty' => true]);
            echo $this->Form->input('series_id', ['options' => $series, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
