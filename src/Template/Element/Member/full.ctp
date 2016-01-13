
<!-- Element/Member/full.ctp -->
<section class="member row">
	<div class="columns small-12 medium-9 text">
		<?= $this->element('Member/text'); ?>
		<section class="addresses">
		<?php
			$this->set('addresses', $member->addresses);
			echo $this->element('Address/' . $element_management['address']);
		?>
		</section>
		<section class="contacts">
		<?php
			$this->set('contacts', $member->contacts);
			echo $this->element('Contact/' . $element_management['contact']);
		?>
		</section>
	</div>
</section>
<!-- END Element/Member/full.ctp -->
