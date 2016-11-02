<!-- Element/Member/refine.ctp -->
<?php
	$members = isset($member->proxy_group->members) ? $member->proxy_group->members : [];
    $mode = ($editing) ? 'refine' : 'review';
?>
<section class="member">
    <?= $this->element('Member/heading') ?>
	<!----------------------------->
    <div class="member_left">
        <section class="identity">
            <?= $this->element("Member/identity_$mode") ?>
        </section>
        <div class="contacts">
            <?= $this->element("Contact/$mode") ?>
        </div>
    </div>
	<!----------------------------->
    <div class="member_right">
        <div class="addresses">
            <?= $this->element("Address/$mode") ?>
        </div>
        <div class="groups">
            <?= $this->element("Group/$mode") ?>
        </div>
    </div>
	<!----------------------------->
	<div class="members"><!--Member of this (if it's a group)-->
		<?= $this->element("Member/many", ['members' => $members]) ?>
	</div>
	<!----------------------------->
    <div class="member_controls">
        <?= $this->element("Member/{$mode}_controls") ?>
    </div>
	<?php if (isset($dispositions) && !empty($dispositions)) : ?>
	<div class="dispositions">
		
		<?php 
		foreach($dispositions as $disposition) : 
			$this->set('disposition', $disposition);
		?>
		<?= $this->Html->para(NULL, $disposition->label . ' ' . $disposition->name); ?>

		<?php endforeach; ?>
		
	</div>
	<?php endif; ?>
</section>
	<?php // osd($dispositions->toArray()); ?>
<!-- END Element/Member/refine.ctp -->
