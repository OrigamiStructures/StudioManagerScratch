<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Image'), ['action' => 'edit', $image->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Image'), ['action' => 'delete', $image->id], ['confirm' => __('Are you sure you want to delete # {0}?', $image->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Images'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Image'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Artworks'), ['controller' => 'Artworks', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Artwork'), ['controller' => 'Artworks', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Formats'), ['controller' => 'Formats', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Format'), ['controller' => 'Formats', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="images view large-9 medium-8 columns content">
    <h3><?= h($image->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $image->has('user') ? $this->Html->link($image->user->id, ['controller' => 'Users', 'action' => 'view', $image->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Image File') ?></th>
            <td><?= h($image->image_file) ?></td>
        </tr>
        <tr>
            <th><?= __('Image Dir') ?></th>
            <td><?= h($image->image_dir) ?></td>
        </tr>
        <tr>
            <th><?= __('Mimetype') ?></th>
            <td><?= h($image->mimetype) ?></td>
        </tr>
        <tr>
            <th><?= __('Title') ?></th>
            <td><?= h($image->title) ?></td>
        </tr>
        <tr>
            <th><?= __('Alt') ?></th>
            <td><?= h($image->alt) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($image->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Filesize') ?></th>
            <td><?= $this->Number->format($image->filesize) ?></td>
        </tr>
        <tr>
            <th><?= __('Width') ?></th>
            <td><?= $this->Number->format($image->width) ?></td>
        </tr>
        <tr>
            <th><?= __('Height') ?></th>
            <td><?= $this->Number->format($image->height) ?></td>
        </tr>
        <tr>
            <th><?= __('Date') ?></th>
            <td><?= $this->Number->format($image->date) ?></td>
        </tr>
        <tr>
            <th><?= __('Upload') ?></th>
            <td><?= $this->Number->format($image->upload) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($image->modified) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($image->created) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Artworks') ?></h4>
        <?php if (!empty($image->artworks)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Image Id') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('Description') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($image->artworks as $artworks): ?>
            <tr>
                <td><?= h($artworks->id) ?></td>
                <td><?= h($artworks->created) ?></td>
                <td><?= h($artworks->modified) ?></td>
                <td><?= h($artworks->user_id) ?></td>
                <td><?= h($artworks->image_id) ?></td>
                <td><?= h($artworks->title) ?></td>
                <td><?= h($artworks->description) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Artworks', 'action' => 'view', $artworks->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Artworks', 'action' => 'edit', $artworks->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Artworks', 'action' => 'delete', $artworks->id], ['confirm' => __('Are you sure you want to delete # {0}?', $artworks->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Formats') ?></h4>
        <?php if (!empty($image->formats)): ?>
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
            <?php foreach ($image->formats as $formats): ?>
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
    <div class="related">
        <h4><?= __('Related Members') ?></h4>
        <?php if (!empty($image->members)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('Name') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Image Id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($image->members as $members): ?>
            <tr>
                <td><?= h($members->id) ?></td>
                <td><?= h($members->created) ?></td>
                <td><?= h($members->modified) ?></td>
                <td><?= h($members->name) ?></td>
                <td><?= h($members->user_id) ?></td>
                <td><?= h($members->image_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Members', 'action' => 'view', $members->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Members', 'action' => 'edit', $members->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Members', 'action' => 'delete', $members->id], ['confirm' => __('Are you sure you want to delete # {0}?', $members->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
