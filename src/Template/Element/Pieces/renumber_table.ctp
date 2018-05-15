<!-- Element/Pieces/renumber_table.ctp -->
	
<table>
	<caption>
		<?= $caption ?>
	</caption>
	<thead>
		<tr>
			<th>New number</th>
			<th>Original number</th>
			<th>Format</th>
			<th>Assignment</th>
			<th>Dispositions</th>
		</tr>
		<tr class="hidden">
			<th colspan="2">
				<?= $this->element('Pieces/renumber_approval_form') ?>
			</th>
			<th colspan="3">
				<?= $this->element('Pieces/renumber_cancel_form') ?>
			</th>
		</tr>
	</thead>
	<?= $this->Form->create('Pieces', ['id' => 'request']); ?>
	<tbody>
		<?= $this->element('Pieces/renumber_rows'); ?>
	</tbody>
	<?= $this->Form->end(); ?>
</table>
	
<!-- END Element/Edition/renumber_table.ctp -->
