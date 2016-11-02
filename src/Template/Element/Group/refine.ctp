<!-- Element/Group/refine.ctp -->
<?php
if(!empty($member['groups'])):
    $memberships = $member['groups'];
?>

<fieldset>
    <legend>Group Membership</legend>
    
<?php
    foreach ($memberships as $key => $group) {
        $this->set(compact('group', 'key'));
        echo $this->Html->tag('section', $this->element('Group/fieldset'), ['class' => 'group']);
    }
?>
    
</fieldset>

<?php
endif;

$this->set('type', 'groups');
echo $this->element('Contact/add');
?>
<!-- END Element/Group/refine.ctp -->
