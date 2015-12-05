<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Edition'), ['action' => 'edit', $edition->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Edition'), ['action' => 'delete', $edition->id], ['confirm' => __('Are you sure you want to delete # {0}?', $edition->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Editions'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Edition'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Artworks'), ['controller' => 'Artworks', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Artwork'), ['controller' => 'Artworks', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Formats'), ['controller' => 'Formats', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Format'), ['controller' => 'Formats', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Pieces'), ['controller' => 'Pieces', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Piece'), ['controller' => 'Pieces', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="editions view large-9 medium-8 columns content">
    <h3><i class="glyphicon icon-duplicate"></i><?= h($edition->name) ?></h3>
	<div class="row">
		<p class="columns large-3">3 columns</p>
		<p class="columns large-6">6 columns</p>
		<p class="columns large-3">3 columns</p>
	</div>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $edition->has('user') ? $this->Html->link($edition->user->id, ['controller' => 'Users', 'action' => 'view', $edition->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Name') ?></th>
            <td><?= h($edition->name) ?></td>
        </tr>
        <tr>
            <th><?= __('Type') ?></th>
            <td><?= h($edition->type) ?></td>
        </tr>
        <tr>
            <th><?= __('Artwork') ?></th>
            <td><?= $edition->has('artwork') ? $this->Html->link($edition->artwork->title, ['controller' => 'Artworks', 'action' => 'view', $edition->artwork->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($edition->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Quantity') ?></th>
            <td><?= $this->Number->format($edition->quantity) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($edition->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($edition->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Formats') ?></h4>
        <?php if (!empty($edition->formats)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Edition Id') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('Description') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($edition->formats as $formats): ?>
            <tr>
                <td><?= h($formats->id) ?></td>
                <td><?= h($formats->created) ?></td>
                <td><?= h($formats->modified) ?></td>
                <td><?= h($formats->user_id) ?></td>
                <td><?= h($formats->edition_id) ?></td>
                <td><?= h($formats->title) ?></td>
                <td><?= h($formats->description) ?></td>
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
    <div class="related">
        <h4><?= __('Related Pieces') ?></h4>
        <?php if (!empty($edition->pieces)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Edition Id') ?></th>
                <th><?= __('Format Id') ?></th>
                <th><?= __('Number') ?></th>
                <th><?= __('Quantity') ?></th>
                <th><?= __('Made') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($edition->pieces as $pieces): ?>
            <tr>
                <td><?= h($pieces->id) ?></td>
                <td><?= h($pieces->created) ?></td>
                <td><?= h($pieces->modified) ?></td>
                <td><?= h($pieces->user_id) ?></td>
                <td><?= h($pieces->edition_id) ?></td>
                <td><?= h($pieces->format_id) ?></td>
                <td><?= h($pieces->number) ?></td>
                <td><?= h($pieces->quantity) ?></td>
                <td><?= h($pieces->made) ?></td>
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
