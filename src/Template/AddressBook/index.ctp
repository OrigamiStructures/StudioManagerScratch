<?php foreach ($results->getData() as $entity) {
    if($entity->isGroup()){
        echo $this->Html->tag('h2', 'SKIPPED GROUP');
        continue;
    }
    echo $this->Html->tag('h3', $entity->rootDisplayValue());
    echo $this->Html->tag('h2', $entity->IDs());
    echo $this->Html->link;
//    osd($entity);
    ?>

	<li>Contacts
		<ul>
			<?php foreach ($entity->contacts() as $contact) : ?>
			<li><?= $contact ?></li>
			<?php endforeach; ?>
		</ul>
	</li>
    <li>Addresses
        <ul>
            <?php foreach ($entity->addresses() as $address) : ?>
                <li><?= $address ?></li>
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
