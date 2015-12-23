<!-- Element/Edition/full.ctp -->
<section class="edtion">
	<div class="row">
		<div class="columns small-12 medium-9 description">
            <?php
                foreach ($editions as $edition) {
                    echo $this->Html->tag('h4', $edition->displayTitle);
                    $this->set('formats', $edition->formats);
                    echo $this->element('Format/' . $element_management['format']);
                }
            ?>
		</div>
	</div>
</section>