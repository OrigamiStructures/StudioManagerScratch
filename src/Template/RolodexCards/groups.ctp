<?php
foreach($categoryCards->all() as $id => $card) {
	echo "<h1>{$card->name()}</h1>";
	
	if ($card->isMember()) {
		echo "<h2>Memberships</h2>";
		$output = collection($card->memberships())
				->reduce(function($membershipList, $name) {
			$membershipList .= '<p>' . $name . '</p>';
			return $membershipList;
		}, '');
		echo $output;
	}
	
	if ($card->hasMembers()) {
		echo "<h2>Members</h2>";
		$output = collection($card->members())
				->reduce(function($memberList, $name) {
			$memberList .= '<p>' . $name . '</p>';
			return $memberList;
		}, '');
		echo $output;
	}
	
} 