<?php
//osd($artwork_element);//die;
//osd($this->viewVars, 'viewVars');
//osd($editions);
?>
<div class="row">
	<section class="columns small-12 medium-6 edit">
		<!-- This side gets uuids on the inputs and onchange methods that can create mimic the server side 'display value' logic -->
		<?= $this->Form->create(); ?>
		<?= $this->element("Artwork/{$artwork_element}"); ?>
		<?= $this->element("Edition/{$edition_element}"); ?>
		<?= $this->element("Format/{$format_element}"); ?>	
		<?= $this->Form->submit(); ?>
		<?= $this->Form->end(); ?>
	</section>
	<section class="columns small-12 medium-6 preview">
		<!-- This side receives the 'display values' generated by the edit side allowing artists to preview their results -->
	</section>
</div>
