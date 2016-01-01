<!-- Element/Edition/describe.ctp -->
<section class="edition">
	<div class="text">
<?php
/**
 * There may be a better way to do this logic.
 * This is not called in a loop (at least in edit/create contexts) and so 
 * the variable required downstream is not know. 
 */
if (!isset($edition) && $SystemState->isKnown('edition')) {
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
