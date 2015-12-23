<!-- Element/Format/full.ctp -->
<section class="edtion">
	<div class="row">
		<div class="columns small-12 medium-9 description">
            <?php
                foreach ($formats as $format) {
                    echo $this->Html->tag('h5', $format->displayTitle);
                }
            ?>
		</div>
	</div>
</section>