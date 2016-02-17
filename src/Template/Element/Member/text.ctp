
<!-- Element/Member/text.ctp -->
<?php
    if(isset($member->first_name)){
        echo $this->Html->tag('h2', h($member->name));
    }
?>
<!-- END Element/Member/text.ctp -->
