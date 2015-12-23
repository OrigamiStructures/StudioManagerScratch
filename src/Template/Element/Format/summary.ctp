<!-- Element/Format/summary.ctp -->
<section class="edtion">
	<div class="row">
		<div class="columns small-12 medium-9 description">
            <?php
                $count = count($formats);
                $word = ($count > 1) ? 'formats' : 'format';
                echo $this->Html->tag('h4', "contains $count $word" );
            ?>
		</div>
	</div>
</section>