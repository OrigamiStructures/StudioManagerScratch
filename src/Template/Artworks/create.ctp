<!-- Template/Artwork/new.ctp -->
<section class="artwork">
	<div class="row artwork">
		<div class="columns small-12 medium-5 medium-offset-1">
            <?php
            echo $this->Form->create($artwork, ['type' => 'file']);
                echo $this->element('Artwork/' . $element_management['artwork']);
//                echo $this->element('Series/' . $element_management['series']);
                echo $this->element('Edition/' . $element_management['edition']);
                echo $this->element('Format/' . $element_management['format']);
				echo $this->element('Image/fieldset');
                echo $this->Form->submit();
            echo $this->Form->end();
            ?>
        </div>
    </div>
</section>
