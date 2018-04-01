<p>
	Variable <em>artwork</em> might have more than one node in <em>editions</em>
	and so a scan will have to be done to find the correct one to display.
</p>
<p>
	There are open questions:
</p>
<ul>
	<li>Show pieces from <em>Edition</em> or <em>Format</em></li>
	<li><em>Edition</em> approach would let artists move pieces to new formats.</li>
</ul>
	<?php
//osd($SystemState->queryArg());
//debug($artwork->editions[1]['pieces']);
	echo $this->Form->select('formats',$formats);
osd($formats);
//osd($series);
//osd($subscriptions);