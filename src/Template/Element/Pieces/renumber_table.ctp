<!-- Element/Pieces/renumber_table.ctp -->
<div>
	<?= $this->element('Pieces/renumber_approval_form') ?>
	<?= $this->element('Pieces/renumber_cancel_form') ?>	
</div>
<?= $this->Form->create('Pieces', ['id' => 'request']); ?>
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
	</thead>
	<tbody>
		<?= $this->element('Pieces/renumber_rows'); ?>
	</tbody>
</table>
<?= $this->Form->end(); ?>
	
<!-- END Element/Edition/renumber_table.ctp -->
