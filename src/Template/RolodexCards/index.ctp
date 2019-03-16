<?php

osd($rolodexCards->toArray());

foreach($rolodexCards as $card) {
	if(!empty($card->memberships)){
		foreach($card->memberships as $membership) {
			echo('<p>'.$membership->name().'</p>');
		}
	} 
}