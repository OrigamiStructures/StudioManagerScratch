<!-- Element/Group/fieldset.ctp -->
<?php
$removeButton = $this->Html->link($this->Html->tag('i', '', ['class' => 'fi-x large']), [
    'controller' => 'GroupsMembers',
    'action' => 'delete',
    '?' => [
        'member' => $member->id,
        'group' => $member->groups[$key]->id
    ]
        ], [
    'escape' => FALSE,
    'title' => 'Remove',
        ]);
?>

<div class="input-with-toollabel">
    <label for="groups-<?= $key ?>-id">

        <?=
        $removeButton
        ?>
    </label>
    <?=
    $this->Form->input("groups.$key.id", [
        'options' => $groups,
        'type' => 'select',
        'label' => FALSE,
        'empty' => 'Choose a group',
    ]);
    ?>
</div>
<!-- END Element/Group/fieldset.ctp -->
