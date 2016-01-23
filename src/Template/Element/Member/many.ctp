<!-- Element/Member/many.ctp -->
<?php foreach ($members as $member): ?>
<?php $this->set('member', $member); ?>
    <div class="member row">
	<div class="columns small-12 medium-3 text">
		<?= $this->element('Member/text'); ?>
	</div>
    <div class="columns small-12 medium-3 control">
        <?= $this->element('Member/controls'); ?>
    </div>
</div>

<?php endforeach;?>
<!-- END Element/Member/many.ctp -->
