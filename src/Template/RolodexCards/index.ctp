<?php

//osd($rolodexCards);
$search = ($rolodexCards->accessArgs()->setLayer('identity'));
foreach($rolodexCards->all() as $id => $card) {
	osd($card->name());
	osd($card->memberships);
//	osd($card->distinct('memberships', 'name'));
//	osd($card->primaryEntity());
//	echo "<h1>{$card->name()}</h1>";
//	if ($card->hasMemberships()) {
//		collection($card->memberships->distinct())->map(function($entity){
//			echo '<p>' . $membership->name() . '</p>';
//		});
//	}
}