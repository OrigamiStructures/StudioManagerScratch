<?php if ((boolean) $piece->disposition_count) : ?>
<!-- Element/Pieces/disposition_event_rows.ctp -->
		<?php foreach($piece->dispositions as $disposition) : ?>
<tr class="disposition-detail">
	<td>&nbsp;</td>
	<td colspan="2"><?= $disposition->label . ' ' . $disposition->name; ?></td>
	<td colspan="2"><?= $disposition->member_name; ?></td>
</tr>
		<?php endforeach; ?>
<!-- END Element/Pieces/disposition_event_rows.ctp -->
<?php endif; ?>
