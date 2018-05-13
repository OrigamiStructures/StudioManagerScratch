<?php
$edition = $providers['edition'];
$artwork = $providers['edition']['artwork'];
$this->set(compact('edition', 'artwork'));
$this->loadHelper('EditionFactory');

// from Edition/text.ctp
$q = [
	'controller' => 'editions', 
	'?' => [
		'artwork' => $artwork->id,
		'edition' => $edition->id,
	]];
$l = $this->InlineTools->inlineReviewRefine($q);
$edition_index = isset($edition_index) ? $edition_index : 0 ; 

?>
<div class="artworks">
	
	<section class="artwork">
		<?= $this->element('Artwork/describe'); ?>

		<div class="editions">
			<section class="edition focus">
				<div class="text">
					<!--Edition/text.ctp-->
					<?= $this->Form->input("editions.$edition_index.id", 
							['type' => 'hidden', 'value' => $edition->id]); ?>

					<?php
					if (!empty($edition->series_id)) {
						echo $this->Html->tag('h3', "Part of the {$edition->series->title} Series");
					}
					?>

					<?= $this->Html->tag('h2', "{$l}$edition->displayTitle"); ?>
					<!--END Edition/text.ctp-->
				</div>

				<div class="pieces">
					
					<!--Original page content-->
					
<?php
if ($renumber_summary) :
?>
	<?php 
	if ($renumber_summary->errors()) { 
		$s = $renumber_summary->errorCount() === 1 ? '' : 's';
		echo "<p class='error'>Correct the {$renumber_summary->errorCount()} error$s below.</p>";
	} 
	 
	if ($renumber_summary->summaries()) {
		foreach ($renumber_summary->summaries() as $message) {
		 echo "<p>$message</p>";
		}
	
}?>

	<?= $this->Form->create('Pieces', ['id' => 'confirm']); ?>

<?php if(!$renumber_summary->errors()) : ?>
		<?= $this->Form->button('approve', ['type' => 'submit']); ?>
		<?= $this->Form->input('do_move', ['value' => TRUE, 'type' => 'hidden']); ?>
<?php endif; ?>

	<?= $this->Form->end(); ?>
	
<?php endif ?>
<?= $this->Form->create('', ['id' => 'cancel', 'formmethod' => 'post']); ?>
	<?= $this->Form->input('cancel', ['type' => 'hidden', 'value' => TRUE])?>
<?= $this->Form->end(); ?>

<?= $this->Form->create('Pieces', ['id' => 'request']); ?>

	<?= $this->element('Pieces/renumber_table', ['caption' => 'Pieces in this edition']); ?>

<?= $this->Form->end(); ?>
					
					<!--END Original page content-->
					<?php //echo $this->element('Edition/pieces'); ?>
				</div>
				
				<div class="formats">
					<?php //$this->set('formats', $edition->formats); ?>
					<?php //echo $this->element('Format/many'); ?>
				</div>
			</section>
		</div>
	</section>
	
</div>

