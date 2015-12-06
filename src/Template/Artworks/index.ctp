<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Artwork'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Editions'), ['controller' => 'Editions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Edition'), ['controller' => 'Editions', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="artworks index large-9 medium-8 columns content">
    <h3><?= __('Artworks') ?></h3>
<table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('created') ?></th>
                <th><?= $this->Paginator->sort('modified') ?></th>
                <th><?= $this->Paginator->sort('user_id') ?></th>
                <th><?= $this->Paginator->sort('title') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($artworks as $artwork): ?>
            <tr>
                <td><?= $this->Number->format($artwork->id) ?></td>
                <td><?= h($artwork->created) ?></td>
                <td><?= h($artwork->modified) ?></td>
                <td><?= $artwork->has('user') ? $this->Html->link($artwork->user->id, ['controller' => 'Users', 'action' => 'view', $artwork->user->id]) : '' ?></td>
                <td>
					<i class="glyphicon icon-asterisk" data-dropdown="hover<?= $artwork->id;?>" data-options="is_hover:true; hover_timeout:5000"> </i> <?= h($artwork->title) ?>

					<ul id="hover<?= $artwork->id;?>" class="f-dropdown" data-dropdown-content>
					  <li><a href="#">Add an Edition (non-functioning)</a></li>
<!--					  <li><a href="#">This is another</a></li>
					  <li><a href="#">Yet another</a></li>-->
					</ul>
				</td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $artwork->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $artwork->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $artwork->id], ['confirm' => __('Are you sure you want to delete # {0}?', $artwork->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
