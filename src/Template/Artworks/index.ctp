<?php
    /* @var \App\Model\Lib\StackSet $results */
    /* @var \App\View\AppView $this */


    foreach ($results->getData() as $artwork) {
        /* @var \App\Model\Entity\ArtStack $artwork */

    echo $this->Html->tag('h3', $artwork->rootDisplayValue());
    echo $this->Html->para('', $artwork->artists());
    ?>

    <li>Editions
        <ul>
            <?php foreach ($artwork->getLayer('editions')->toArray() as $edition) : ?>
                <li><?= $edition->displayTitle; ?></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <li>Pieces
        <ul>
            <?php foreach ($artwork->getLayer('pieces')->toArray() as $piece) : ?>
                <li><?= $piece->displayTitle; ?></li>
            <?php endforeach; ?>
        </ul>
    </li>

<?php } ?>
<?php
echo $this->element('Artwork/search'/*, ['identitySchema' => $identitySchema]*/);
