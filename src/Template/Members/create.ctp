<!-- Template/Member/create.ctp -->
<section class="member">
	<div class="row member">
		<div class="columns small-12 medium-5 medium-offset-1">
            <?php
            echo $this->Form->create($member);
                echo $this->element('Member/refine');
                echo $this->element('Address/refine');
                echo $this->element('Contact/refine');
                echo $this->Form->submit();
            echo $this->Form->end();
            ?>
        </div>
    </div>
</section>

