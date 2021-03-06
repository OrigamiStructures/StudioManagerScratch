<!-- Element/Group/review.ctp -->
<?php
$groups_count = count($member->groups);
$this->set('groups_count', $groups_count);
if($editing){
    echo $this->element('Group/add');
}

echo $this->Html->tag('h2', 'Groups');
if(!empty($member->groups)):
    foreach ($member->groups as $key => $group) {
//        $this->set('group', $group);
//        echo $this->element('Group/display');
        $this->set('member', $group->proxy_member);
        echo $this->element('Member/text');
    }
else :
	echo '<p>No memberships</p>';

endif;
//The member variable had to be reset to the group's proxy member to allow
//us to reuse the element Member/text for the group display name

$this->set('member', $member);
?>
<!-- END Element/Group/review.ctp -->
