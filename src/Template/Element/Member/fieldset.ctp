<!-- Element/Member/fieldset.ctp -->
<fieldset>
	<legend>Member Information</legend>
    <?= $this->Form->input("id"); ?>
    <?= $this->Form->input("first_name");?>
    <?= $this->Form->input("last_name");?>
    <?= $this->Form->input("type");?>
</fieldset>
