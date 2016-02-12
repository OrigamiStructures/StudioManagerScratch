<?php
$editing = FALSE;
$ArtStackElement = $this->loadHelper('App\View\Helper\ArtStackElementHelper');
$this->set(compact('ArtStackElement', 'editing'));
function tf($value) {
	if (is_null($value)) {
		return '';
	}
	return (boolean) $value ? 'True' : 'False';
}
//osd($this->request->query);
//osd($artwork); die;
?>
<?= $this->element('Artwork/text'); ?>
<?php
foreach ($artwork->editions as $edition) {
	$this->set('edition', $edition);
	echo $this->element('Artwork/validation/edition_table');
}
?>
<!-- 
<table>
	<caption>
		Edition number 1
	</caption>
	<thead>
		<tr>
			<th>Heading labels</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				a data point
			</td>
		</tr>
	</tbody>
</table>
<table>
	<caption>
		Edition number 2
	</caption>
	<thead>
		<tr>
			<th>Heading labels</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				a data point
			</td>
		</tr>
	</tbody>
</table> -->



<!-- The 'review' rendering of this artwork -->
<div class="artworks">
<?= $this->element('Artwork/full');?>
</div>
