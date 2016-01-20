<!-- Template/Member/create.ctp -->
<section class="member">
	<div class="row member">
		<div class="columns small-12 medium-5 medium-offset-1">
            <?php
            echo $this->Form->create($member);
                echo $this->element('Member/refine');
                echo $this->element('Address/refine');
                echo $this->element('Contact/refine');
                echo $this->Form->button('Submit', ['type' => 'submit', 'class' => 'button success']);
                echo $this->Html->link('Cancel', ['action' => 'review', '?' => ['member' => $member->id]], ['class' => 'button secondary', 'confirm' => 'Are you sure you want to cancel?']);
            echo $this->Form->end();
            ?>
        </div>
    </div>
</section>

