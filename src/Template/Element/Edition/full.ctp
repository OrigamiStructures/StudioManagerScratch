<!-- Element/Edition/full.ctp -->
<section class="edtion">
	<div class="row">
		<div class="columns small-12 medium-9 description">
            <?php
                foreach ($editions as $edition_count => $edition) {
                    echo $this->Html->tag('h2', $edition->displayTitle);
                    $this->set('formats', $edition->formats);
                    $this->set('edition_count', $edition_count);
//					echo $this->Form->input("editions.$edition_count.id");
                    echo $this->element('Format/' . $element_management['format']);
                }
            ?>
		</div>
	</div>
</section>