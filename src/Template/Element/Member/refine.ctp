<!-- Element/Member/refine.ctp -->
<section class="row member">
<?php
    echo $this->Html->div('memberDisplay', $this->element('Member/text'), ['display' => 'hidden']);
    echo $this->Html->div('memberFieldset', $this->element('Member/fieldset'));
?>
</section>
<!-- END Element/Member/refine.ctp -->
