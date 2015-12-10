<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit User'), ['action' => 'edit', $user->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete User'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete # {0}?', $user->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Artworks'), ['controller' => 'Artworks', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Artwork'), ['controller' => 'Artworks', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Dispositions'), ['controller' => 'Dispositions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Disposition'), ['controller' => 'Dispositions', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Editions'), ['controller' => 'Editions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Edition'), ['controller' => 'Editions', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Formats'), ['controller' => 'Formats', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Format'), ['controller' => 'Formats', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Groups'), ['controller' => 'Groups', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Group'), ['controller' => 'Groups', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Groups Members'), ['controller' => 'GroupsMembers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Groups Member'), ['controller' => 'GroupsMembers', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Images'), ['controller' => 'Images', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Image'), ['controller' => 'Images', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Locations'), ['controller' => 'Locations', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Location'), ['controller' => 'Locations', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Pieces'), ['controller' => 'Pieces', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Piece'), ['controller' => 'Pieces', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Series'), ['controller' => 'Series', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Series'), ['controller' => 'Series', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Subscriptions'), ['controller' => 'Subscriptions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Subscription'), ['controller' => 'Subscriptions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="users view large-9 medium-8 columns content">
    <h3><?= h($user->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Username') ?></th>
            <td><?= h($user->username) ?></td>
        </tr>
        <tr>
            <th><?= __('Password') ?></th>
            <td><?= h($user->password) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($user->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Member Id') ?></th>
            <td><?= $this->Number->format($user->member_id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($user->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($user->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Members') ?></h4>
        <?php if (!empty($user->members)): ?>
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
            <?php foreach ($user->members as $members): ?>
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
    <div class="related">
        <h4><?= __('Related Artworks') ?></h4>
        <?php if (!empty($user->artworks)): ?>
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
            <?php foreach ($user->artworks as $artworks): ?>
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
        <h4><?= __('Related Dispositions') ?></h4>
        <?php if (!empty($user->dispositions)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Member Id') ?></th>
                <th><?= __('Location Id') ?></th>
                <th><?= __('Piece Id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->dispositions as $dispositions): ?>
            <tr>
                <td><?= h($dispositions->id) ?></td>
                <td><?= h($dispositions->created) ?></td>
                <td><?= h($dispositions->modified) ?></td>
                <td><?= h($dispositions->user_id) ?></td>
                <td><?= h($dispositions->member_id) ?></td>
                <td><?= h($dispositions->location_id) ?></td>
                <td><?= h($dispositions->piece_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Dispositions', 'action' => 'view', $dispositions->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Dispositions', 'action' => 'edit', $dispositions->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Dispositions', 'action' => 'delete', $dispositions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $dispositions->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Editions') ?></h4>
        <?php if (!empty($user->editions)): ?>
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
            <?php foreach ($user->editions as $editions): ?>
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
    <div class="related">
        <h4><?= __('Related Formats') ?></h4>
        <?php if (!empty($user->formats)): ?>
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
            <?php foreach ($user->formats as $formats): ?>
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
        <h4><?= __('Related Groups') ?></h4>
        <?php if (!empty($user->groups)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Name') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->groups as $groups): ?>
            <tr>
                <td><?= h($groups->id) ?></td>
                <td><?= h($groups->created) ?></td>
                <td><?= h($groups->modified) ?></td>
                <td><?= h($groups->user_id) ?></td>
                <td><?= h($groups->name) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Groups', 'action' => 'view', $groups->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Groups', 'action' => 'edit', $groups->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Groups', 'action' => 'delete', $groups->id], ['confirm' => __('Are you sure you want to delete # {0}?', $groups->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Groups Members') ?></h4>
        <?php if (!empty($user->groups_members)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Group Id') ?></th>
                <th><?= __('Member Id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->groups_members as $groupsMembers): ?>
            <tr>
                <td><?= h($groupsMembers->id) ?></td>
                <td><?= h($groupsMembers->created) ?></td>
                <td><?= h($groupsMembers->modified) ?></td>
                <td><?= h($groupsMembers->user_id) ?></td>
                <td><?= h($groupsMembers->group_id) ?></td>
                <td><?= h($groupsMembers->member_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'GroupsMembers', 'action' => 'view', $groupsMembers->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'GroupsMembers', 'action' => 'edit', $groupsMembers->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'GroupsMembers', 'action' => 'delete', $groupsMembers->id], ['confirm' => __('Are you sure you want to delete # {0}?', $groupsMembers->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Images') ?></h4>
        <?php if (!empty($user->images)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Image') ?></th>
                <th><?= __('Image Dir') ?></th>
                <th><?= __('Mimetype') ?></th>
                <th><?= __('Filesize') ?></th>
                <th><?= __('Width') ?></th>
                <th><?= __('Height') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('Date') ?></th>
                <th><?= __('Alt') ?></th>
                <th><?= __('Upload') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->images as $images): ?>
            <tr>
                <td><?= h($images->id) ?></td>
                <td><?= h($images->modified) ?></td>
                <td><?= h($images->created) ?></td>
                <td><?= h($images->user_id) ?></td>
                <td><?= h($images->image) ?></td>
                <td><?= h($images->image_dir) ?></td>
                <td><?= h($images->mimetype) ?></td>
                <td><?= h($images->filesize) ?></td>
                <td><?= h($images->width) ?></td>
                <td><?= h($images->height) ?></td>
                <td><?= h($images->title) ?></td>
                <td><?= h($images->date) ?></td>
                <td><?= h($images->alt) ?></td>
                <td><?= h($images->upload) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Images', 'action' => 'view', $images->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Images', 'action' => 'edit', $images->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Images', 'action' => 'delete', $images->id], ['confirm' => __('Are you sure you want to delete # {0}?', $images->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Locations') ?></h4>
        <?php if (!empty($user->locations)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Member Id') ?></th>
                <th><?= __('Name') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->locations as $locations): ?>
            <tr>
                <td><?= h($locations->id) ?></td>
                <td><?= h($locations->created) ?></td>
                <td><?= h($locations->modified) ?></td>
                <td><?= h($locations->user_id) ?></td>
                <td><?= h($locations->member_id) ?></td>
                <td><?= h($locations->name) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Locations', 'action' => 'view', $locations->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Locations', 'action' => 'edit', $locations->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Locations', 'action' => 'delete', $locations->id], ['confirm' => __('Are you sure you want to delete # {0}?', $locations->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Pieces') ?></h4>
        <?php if (!empty($user->pieces)): ?>
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
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->pieces as $pieces): ?>
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
    <div class="related">
        <h4><?= __('Related Series') ?></h4>
        <?php if (!empty($user->series)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('Description') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->series as $series): ?>
            <tr>
                <td><?= h($series->id) ?></td>
                <td><?= h($series->created) ?></td>
                <td><?= h($series->modified) ?></td>
                <td><?= h($series->user_id) ?></td>
                <td><?= h($series->title) ?></td>
                <td><?= h($series->description) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Series', 'action' => 'view', $series->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Series', 'action' => 'edit', $series->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Series', 'action' => 'delete', $series->id], ['confirm' => __('Are you sure you want to delete # {0}?', $series->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Subscriptions') ?></h4>
        <?php if (!empty($user->subscriptions)): ?>
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
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->subscriptions as $subscriptions): ?>
            <tr>
                <td><?= h($subscriptions->id) ?></td>
                <td><?= h($subscriptions->created) ?></td>
                <td><?= h($subscriptions->modified) ?></td>
                <td><?= h($subscriptions->user_id) ?></td>
                <td><?= h($subscriptions->title) ?></td>
                <td><?= h($subscriptions->description) ?></td>
                <td><?= h($subscriptions->range_flag) ?></td>
                <td><?= h($subscriptions->range_start) ?></td>
                <td><?= h($subscriptions->range_end) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Subscriptions', 'action' => 'view', $subscriptions->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Subscriptions', 'action' => 'edit', $subscriptions->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Subscriptions', 'action' => 'delete', $subscriptions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subscriptions->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
