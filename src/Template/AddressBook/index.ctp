<?php
/* @var View $this */
echo $this->Paginator->prev() . ' || ' . $this->Paginator->next();

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
