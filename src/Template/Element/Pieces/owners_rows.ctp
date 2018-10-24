<!-- Element/Edition/owners_rows.ctp -->
<?php
/**
 * For use when the pieces table is show in an Editon or Format context and 
 * the only pieces displayed are the ones for that context
 */
foreach($pieces as $piece) :
?>
	<tr>
		<?php 
		if (\App\Lib\EditionTypeMap::isNumbered($edition->type)) : ;
		?>
		<td><?= $piece->number; ?></td>
		<?php 
		 endif;
		 ?>
		<td><?= $piece->quantity; ?></td>
		<td><?= (boolean) $piece->disposition_count ? $piece->disposition_count . ' events' : '-'; ?></td>
		<td><?= $piece->collected ? 'Yes' : '-'; ?></td>
	</tr>
<?php
endforeach;
?>
<!-- END Element/Edition/owners_rows.ctp -->
