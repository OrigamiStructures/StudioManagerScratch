<!-- Element/Member/fieldset.ctp -->
<fieldset>
	<legend>Member Information</legend>
    <?= $this->Form->input("id"); ?>
    <?= $this->Form->input("group.id", ['type' => 'hidden']); ?>
    <?php
        $fnameLabel = ($member->type != MEMBER_TYPE_PERSON) ? 'Name' : 'First Name';
    ?>
    <?= $this->Form->input("first_name", ['label' => $fnameLabel]);?>
    <?php
        if($member->type == MEMBER_TYPE_PERSON){
            echo $this->Form->input("last_name");
        }
    ?>
    <?= $this->Form->input("type");?>
</fieldset>
