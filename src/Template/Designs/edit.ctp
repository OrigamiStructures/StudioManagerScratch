<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $design->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $design->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Designs'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="designs form large-9 medium-8 columns content">
    <?= $this->Form->create($design) ?>
    <fieldset>
        <legend><?= __('Edit Design') ?></legend>
        <?php
            echo $this->Form->input('content');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
