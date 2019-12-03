<?php foreach ($results->getData() as $entity) {
    if($entity->isGroup()){
        echo $this->Html->tag('h2', 'SKIPPED GROUP');
        continue;
    }
    echo $this->Html->tag('h3', $entity->rootDisplayValue());
    echo $this->Html->tag('h2', $entity->IDs());
    echo $this->Html->link;
//    osd($entity);

    /* @var \App\Model\Entity\PersonCard $entity */
    /* @var \App\Model\Entity\Contact $contact */
    /* @var \App\Model\Entity\Address $address */
    /* @var \App\Model\Entity\Member $membership */
    ?>

	<li>Contacts
		<ul>
			<?php foreach ($entity->getContacts()->toArray() as $contact) : ?>
			<li><?= $contact->asString() ?></li>
			<?php endforeach; ?>
		</ul>
	</li>
    <li>Addresses
        <ul>
            <?php foreach ($entity->getAddresses()->toArray() as $address) : ?>
                <li><?= $address->asString() ?></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <li>Memberships
        <ul>
            <?php foreach ($entity->getMemberships()->toArray() as $membership) : ?>
                <li><?= $membership->name() ?></li>
            <?php endforeach; ?>
        </ul>
    </li>

<?php } ?>
