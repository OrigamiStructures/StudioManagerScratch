<?php foreach ($results->load() as $entity) {
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
            <?php foreach ($entity->memberships() as $membership) : ?>
                <li><?= $membership ?></li>
            <?php endforeach; ?>
        </ul>
    </li>

<?php } ?>
