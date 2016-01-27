<!-- Element/Contact/fieldset.ctp -->
<fieldset>
	<legend>Contact <?=$key+1?></legend>
    <?= $this->Form->input("contacts.$key.id"); ?>
    <?= $this->Form->input("contacts.$key.label"); ?>
    <?= $this->Form->input("contacts.$key.data");?>
</fieldset>