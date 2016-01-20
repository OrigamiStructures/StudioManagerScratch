<!-- Element/Member/many.ctp -->
<?php
foreach ($members as $member){
//    osd ($member, 'member');
	$this->set('member', $member);
	echo $this->element('Member/full');
}
?>
<!-- END Element/Member/many.ctp -->
