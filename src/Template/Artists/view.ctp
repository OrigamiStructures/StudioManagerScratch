<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Artist $artist
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Artist'), ['action' => 'edit', $artist->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Artist'), ['action' => 'delete', $artist->id], ['confirm' => __('Are you sure you want to delete # {0}?', $artist->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Artists'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Artist'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="artists view large-9 medium-8 columns content">
    <h3><?= h($artist->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Member') ?></th>
            <td><?= $artist->has('member') ? $this->Html->link($artist->member->name(), ['controller' => 'Members', 'action' => 'view', $artist->member->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User Id') ?></th>
            <td><?= h($artist->user_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($artist->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Member User Id') ?></th>
            <td><?= $this->Number->format($artist->member_user_id) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Users') ?></h4>
        <?php if (!empty($artist->users)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Username') ?></th>
                <th scope="col"><?= __('Email') ?></th>
                <th scope="col"><?= __('Password') ?></th>
                <th scope="col"><?= __('First Name') ?></th>
                <th scope="col"><?= __('Last Name') ?></th>
                <th scope="col"><?= __('Token') ?></th>
                <th scope="col"><?= __('Token Expires') ?></th>
                <th scope="col"><?= __('Api Token') ?></th>
                <th scope="col"><?= __('Activation Date') ?></th>
                <th scope="col"><?= __('Tos Date') ?></th>
                <th scope="col"><?= __('Active') ?></th>
                <th scope="col"><?= __('Is Superuser') ?></th>
                <th scope="col"><?= __('Role') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Artist Id') ?></th>
                <th scope="col"><?= __('Member Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($artist->users as $users): ?>
            <tr>
                <td><?= h($users->id) ?></td>
                <td><?= h($users->username) ?></td>
                <td><?= h($users->email) ?></td>
                <td><?= h($users->password) ?></td>
                <td><?= h($users->first_name) ?></td>
                <td><?= h($users->last_name) ?></td>
                <td><?= h($users->token) ?></td>
                <td><?= h($users->token_expires) ?></td>
                <td><?= h($users->api_token) ?></td>
                <td><?= h($users->activation_date) ?></td>
                <td><?= h($users->tos_date) ?></td>
                <td><?= h($users->active) ?></td>
                <td><?= h($users->is_superuser) ?></td>
                <td><?= h($users->role) ?></td>
                <td><?= h($users->created) ?></td>
                <td><?= h($users->modified) ?></td>
                <td><?= h($users->artist_id) ?></td>
                <td><?= h($users->member_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Users', 'action' => 'view', $users->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Users', 'action' => 'edit', $users->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Users', 'action' => 'delete', $users->id], ['confirm' => __('Are you sure you want to delete # {0}?', $users->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
