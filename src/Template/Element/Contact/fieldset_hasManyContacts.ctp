<!-- BEGIN Element/Contact/fieldset_hasManyContacts.ctp -->
<fieldset id="contacts-<?= $key ?>-fieldset">
	<legend>Contact</legend>
    <?= $this->Form->input("contacts.$key.id"); ?>
    <?= $this->Form->input("contacts.$key.label"); ?>
    <?= $this->Form->input("contacts.$key.data");?>
</fieldset>
<!-- END Element/Contact/fieldset_hasManyContacts.ctp -->
