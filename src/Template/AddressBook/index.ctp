<?php foreach ($results->load() as $entity) {
    if($entity->isGroup()){
        echo $this->Html->tag('h2', 'SKIPPED GROUP');
        continue;
    }
    echo $this->Html->tag('h3', $entity->rootDisplayValue());?>

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

<?php } ?>