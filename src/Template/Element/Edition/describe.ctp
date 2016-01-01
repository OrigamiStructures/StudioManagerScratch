<!-- Element/Edition/describe.ctp -->
<section class="edition">
	<div class="text">
<?php
if (!isset($edition) and $SystemState->isKnown('edition')) {
	$i = 0;
	while ($artwork->editions[$i]->id != $SystemState->queryArg('edition')) {
		$i++;
	}
	$this->set('edition', $artwork->editions[$i]);
}
echo $this->element('Edition/text'); 
?>
	</div>
</section>
<!-- END Element/Edition/describe.ctp -->
