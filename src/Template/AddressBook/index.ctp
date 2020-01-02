<?php
/* @var View $this */
/* @var \App\Form\LocalPreferencesForm $prefsForm */

echo $this->Paginator->prev() . ' || ' . $this->Paginator->next();
echo $this->Preferences->pref();

echo $this->Form->create($prefsForm->asContext($prefs->user_id), ['action' => 'setPrefs']);
echo $this->Form->input('paginate.limit');
echo $this->Form->input('paginate.sort.people');
echo $this->Form->submit();
echo $this->Form->end();

osd($prefs);
//osd($people);

foreach ($people->getData() as $person) {
    /* @var \App\Model\Entity\PersonCard $person */

    echo $this->Html->tag('h3', $person->name());
    echo $this->Html->tag('h2', $person->IDs());
    echo $this->Html->link;

    /* @var \App\Model\Entity\PersonCard $entity */
    /* @var \App\Model\Entity\Contact $contact */
    /* @var \App\Model\Entity\Address $address */
    /* @var \App\Model\Entity\Member $membership */
    ?>

	<li>Contacts
		<ul>
			<?php foreach ($person->getContacts()->toArray() as $contact) : ?>
			<li><?= $contact->asString() ?></li>
			<?php endforeach; ?>
		</ul>
	</li>
    <li>Addresses
        <ul>
            <?php foreach ($person->getAddresses()->toArray() as $address) : ?>
                <li><?= $address->asString() ?></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <li>Memberships
        <ul>
            <?php foreach ($person->getMemberships()->toArray() as $membership) : ?>
                <li><?= $membership->name() ?></li>
            <?php endforeach; ?>
        </ul>
    </li>

<?php } ?>
