Arrived
<style>
	div.thing {
		display: inline-grid;
		grid-gap: .5rem;
		padding: .5rem;
		background-color: #008CBA;
		grid-template-columns: 10rem 10rem 2fr;
		margin: 10rem;
	}
	p {
		background-color:#DDD;
		padding: 2rem;
	}
	.c1 {
		/*grid-area: place;*/
	}
	.c2 {
		grid-column: 3;
		display: grid;
		grid-template-columns: 2fr 1fr;
		grid-gap: .25rem;
	}
	
</style>

<div class="thing">
	<p class="c1">content 1</p>
	<p class="c3">content 3</p>
	<p class="c4">content 4</p>
	<p class="c5">content 5</p>
	<div class="c2"><p>thing 2.1</p><p>thing 2.2</p></div>
</div> 



<?php
osd($stuff[0]());
osd($stuff[1]('input val'));
?>
