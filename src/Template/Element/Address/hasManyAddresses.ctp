<!-- BEGIN Element/Address/hasManyAddresses.ctp -->
    <?= $this->Form->input("addresses.$key.id", ['type' => 'hidden']); ?>
    <?= $this->Form->input("addresses.$key.label"); ?>
    <?= $this->Form->input("addresses.$key.address1");?>
    <?= $this->Form->input("addresses.$key.address2");?>
    <?= $this->Form->input("addresses.$key.address3");?>
    <?= $this->Form->input("addresses.$key.city");?>
    <?= $this->Form->input("addresses.$key.state");?>
    <?= $this->Form->input("addresses.$key.zip");?>
    <?= $this->Form->input("addresses.$key.country");?>
<!-- END Element/Address/hasManyAddresses.ctp -->
