<!-- Template/Artwork/elementTest.ctp -->

<?php 
    foreach ($artworks as $artwork):
    echo $this->element('Artwork/view', 
        [
            'artwork' => $artwork,
            'element_management' => $element_management
        ]);
    endforeach;
?>

<!-- Dom standard -->
<!-- As seen from the Artwork index page -->
<header>
    This is the stuff in the main page header, under the menu bar.
</header>
<article class="artwork">
    <header>
        <figure>This is the picture</figure>
        <h1>This is the name of the artwork</h1>
    </header>
    <section class="edition">
        <header>
            <h2>Edition Name</h2>
        </header>
        <p>This is detail or summary stuff on edition 1. It may be suppressed.</p>
        <section class="format">
            <header>
                <h3>Format 1 name</h3>
            </header>
            <p>This is the detail or summary stuff for format 1. It may be suppressed.</p>
        </section>
        <section class="format">
            <header>
                <h3>Format 2 name</h3>
            </header>
            <p>This is the detail or summary stuff for format 2. It may be suppressed.</p>
        </section>
    </section>
    <section class="edition">
        <header>
            <h2>Edition 2 Name</h2>
        </header>
        <p>This is detail or summary stuff on edition 2. It may be suppressed.</p>
    </section>
</article>

<article class="artwork row">
    <figure class="column">This is the picture</figure>
	<section class="column">
		<header>
			<h1>This is the name of the artwork</h1>
		</header>
		<section class="edition">
		</section>
		...
	</section>
</article>
