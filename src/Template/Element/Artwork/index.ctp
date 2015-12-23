<!-- Element/Artwork/index.ctp -->
<section class="artwork">
	<div class="row">
		<div class="columns small-12 medium-3 image">
            <?= $this->Html->image($artwork->image == NULL ? "NoImage.png" : $artwork->image->fullPath); ?>
		</div>
		<div class="columns small-12 medium-9 description">
			<h1><?= $artwork->title; ?></h1>
            <?php
                if(!$artwork->editions == NULL){
                    $edition_count = count($artwork->editions);
                    echo $this->Html->tag('h4', "<span class=\"editionCount\">$edition_count</span> <span class=\"editionCountTag\">total Editions</span>");
                }
            ?>
            <?= $this->Html->element('Edition/index', 
                    [
                        'artwork' => $artwork,
                        'element_management' => $element_management
                    ]); ?>
		</div>
	</div>
</section>