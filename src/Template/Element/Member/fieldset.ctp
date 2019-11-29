<!-- Element/Member/fieldset.ctp -->
<fieldset>
	<legend>Member Information</legend>
    <?= $this->Form->input("id"); ?>
    <?= $this->Form->input("group.id", ['type' => 'hidden']); ?>
    <?php
        $fnameLabel = ($type != MEMBER_TYPE_PERSON) ? 'Name' : 'First Name';
    ?>
    <?= $this->Form->input("first_name", ['label' => $fnameLabel]);?>
    <?php
        if($type == MEMBER_TYPE_PERSON){
            echo $this->Form->input("last_name");
        }
    ?>
    <?= $this->Form->input("member_type", ['type' => 'hidden']);?>
</fieldset>
