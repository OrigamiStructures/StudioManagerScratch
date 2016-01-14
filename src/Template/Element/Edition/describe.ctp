<!-- Element/Edition/describe.ctp -->
<section class="edition">
	<div class="text">
<?php

if (!isset($edition)) {
	$edition_index = $SystemState->isKnown('edition') ?
		$artwork->indexOfRelated('editions', $SystemState->queryArg('edition')) :
		0 ;
	$this->set('edition', $artwork->editions[$edition_index]);
}
echo $this->element('Edition/text'); 
?>
	</div>
</section>
<!-- END Element/Edition/describe.ctp -->
