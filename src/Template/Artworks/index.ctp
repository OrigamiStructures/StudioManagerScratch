<?php
    /* @var \App\Model\Lib\StackSet $results */

    foreach ($results->getData() as $artwork) {
        /* @var \App\Model\Entity\ArtStack $artwork */

    echo $this->Html->tag('h3', $artwork->rootDisplayValue());
    ?>

    <li>Editions
        <ul>
            <?php foreach ($artwork->getLayer('editions') as $edition) : ?>
                <li><?= $edition->displayTitle; ?></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <li>Pieces
        <ul>
            <?php foreach ($artwork->getLayer('pieces') as $piece) : ?>
                <li><?= $piece->displayTitle; ?></li>
            <?php endforeach; ?>
        </ul>
    </li>

<?php } ?>
