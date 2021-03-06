<!-- Template/Editions/create.ctp -->
<section class="artwork">
	<div class="row artwork">
		<div class="columns small-12 medium-5 medium-offset-1">
            <?php
            echo $this->Form->create($artwork, ['type' => 'file']);
                echo $this->element('Artwork/' . $element_management['artwork']);
//              echo $this->element('Series/' . $element_management['series']);
//				echo $this->element('Image/artwork_fieldset');
				echo $this->element('Edition/' . $element_management['edition']);
				
				/**
				 * Create has no editions, refine of a simple artwork has one.
				 * In these caes we let the user edit them directly for simplicity
				 */
				if (count($artwork->editions[0]->formats) < 2 ) {
					echo $this->element('Format/' . $element_management['format']);
					echo $this->Form->submit();
				} else {
					/**
					 * Refine may find an artwork with multiple editions. In this 
					 * case we'll just show text data for the editions rather 
					 * than multiple forms for mass editing 
					 */
					echo $this->Form->submit();
				?>
				<section class="editions">
					<p>This edition includes the following formats</p>
				<?php
					$this->set('formats', $artwork->editions[0]->formats);
					echo $this->element('Format/' . $element_management['format']);
				?>
				</section>
				<?php
				}			
            echo $this->Form->end();
            ?>
        </div>
    </div>
</section>
<?php //			osd($artwork);