<?php
    foreach ($results->load() as $artwork) {
    echo $this->Html->tag('h3', $artwork->rootDisplayValue());
    ?>

    <li>Editions
        <ul>
            <?php foreach ($artwork->find('editions')->load() as $edition) : ?>
                <li><?= $edition->displayTitle; ?></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <li>Pieces
        <ul>
            <?php foreach ($artwork->find('pieces')->load() as $piece) : ?>
                <li><?= $piece->displayTitle; ?></li>
            <?php endforeach; ?>
        </ul>
    </li>

<?php } ?>