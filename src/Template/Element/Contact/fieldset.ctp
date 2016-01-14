<!-- Element/Contact/fieldset.ctp -->
<fieldset>
	<legend>Contact <?=$contactKey+1?></legend>
    <?= $this->Form->input("contacts.$contactKey.id"); ?>
    <?= $this->Form->input("contacts.$contactKey.label"); ?>
    <?= $this->Form->input("contacts.$contactKey.data");?>
</fieldset>
