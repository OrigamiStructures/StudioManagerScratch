<!-- Element/Edition/summary.ctp -->
<section class="edition">
	<div class="row">
		<div class="columns small-12 medium-9 description">
            <?php
                $count = count($editions);
                $word = ($count > 1) ? 'editions' : 'edition';
//                echo $this->Html->tag('h4', "contains $count $word" );
                echo $this->Html->tag('h4', 
						$this->ArtStackTools->links('edition', ['review', 'refine'])
						. $edition->displayTitle );
            ?>
		</div>
	</div>
</section>