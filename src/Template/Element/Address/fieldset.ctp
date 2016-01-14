<!-- Element/Address/fieldset.ctp -->
<fieldset>
	<legend>Address <?=$addressKey+1?></legend>
    <?= $this->Form->input("addresses.$addressKey.id"); ?>
    <?= $this->Form->input("addresses.$addressKey.label"); ?>
    <?= $this->Form->input("addresses.$addressKey.address1");?>
    <?= $this->Form->input("addresses.$addressKey.address2");?>
    <?= $this->Form->input("addresses.$addressKey.address3");?>
    <?= $this->Form->input("addresses.$addressKey.city");?>
    <?= $this->Form->input("addresses.$addressKey.state");?>
    <?= $this->Form->input("addresses.$addressKey.zip");?>
    <?= $this->Form->input("addresses.$addressKey.country");?>
</fieldset>
