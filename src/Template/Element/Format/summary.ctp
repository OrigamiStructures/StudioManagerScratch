<!-- Element/Format/summary.ctp -->
            <?php
                $count = count($formats);
                $word = ($count > 1) ? 'formats' : 'format';
                echo $this->Html->tag('p', "<!-- contains $count $word -->" );
//				echo "<div></div>";
                echo $this->Html->tag('p', $format->displayTitle );
            ?>
