<?php

//osd($rolodexCards);
$search = ($rolodexCards->accessArgs()->setLayer('identity'));
foreach($rolodexCards->all() as $id => $card) {
	osd($card->name);
	osd($card->primaryEntity());
//	echo "<h1>{$card->name()}</h1>";
//	if ($rolodexCards->member($id)->hasMemberships()) {
//		collection($card->memberships->distinct())->map(function($entity){
//			echo '<p>' . $membership->name() . '</p>';
//		});
//	}
}