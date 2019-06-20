<?php foreach ($results->find('artwork')->load() as $entity) {
//    echo $this->Html->tag('h3', $entity->rootDisplayValue());
    osd($entity);
    ?>

    <li>Editions
        <ul>
            <?php //foreach ($entity->find('editions')->load() as $edition) : ?>
                <li><?= 'Edition'; //$edition ?></li>
            <?php //endforeach; ?>
        </ul>
    </li>
    <li>Pieces
        <ul>
            <?php //foreach ($entity->find('pieces')->load() as $piece) : ?>
                <li><?= 'Piece'; //$piece ?></li>
            <?php//endforeach; ?>
        </ul>
    </li>

<?php } ?>