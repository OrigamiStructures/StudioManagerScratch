<!-- Template/Artwork/elementTest.ctp -->

<?php foreach ($artworks as $artwork): ?>
    <?= $this->element('Artwork/index', ['artwork' => $artwork]); ?>
<?php endforeach; ?>
