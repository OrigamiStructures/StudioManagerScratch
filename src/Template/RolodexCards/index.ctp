<?php
foreach($rolodexCards->getData() as $id => $card) {
	echo "<h1>{$card->name()}</h1>";
	$output = collection($card->memberships())
		->reduce(function($membershipList, $name){
			$membershipList .= '<p>' . $name . '</p>';
			return $membershipList;
		},'');
	echo $output;
}
