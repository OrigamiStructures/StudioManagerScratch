<!-- Element/Format/summary.ctp -->
<?php
                $count = count($formats);
                $word = ($count > 1) ? 'formats' : 'format';
                echo $this->Html->tag('p', "<!-- contains $count $word -->" );
//				echo "<div></div>";
                echo $this->Html->tag('p', 
						$this->ArtStackTools->links('format', ['review', 'refine']) . 
						$format->displayTitle );
            ?>
