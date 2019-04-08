<?php
foreach($categoryCards->all() as $id => $card) {
	echo "<h1>{$card->name()}</h1>";
	
	$output = collection($card->contacts())
			->reduce(function($accum, $name) {
		$accum .= '<p>' . $name . '</p>';
		return $accum;
	}, '');
	echo $output;
	
	$output = collection($card->addresses())
			->reduce(function($accum, $name) {
		$accum .= '<p>' . $name . '</p>';
		return $accum;
	}, '');
	echo $output;
	
	if ($card->isMember()) {
		echo "<h2>Memberships</h2>";
		$output = collection($card->memberships())
				->reduce(function($accum, $name) {
			$accum .= '<p>' . $name . '</p>';
			return $accum;
		}, '');
		echo $output;
	}
	
	if ($card->isGroup()) {
		echo "<h2>Members</h2>";
		$output = collection($card->members())
				->reduce(function($accum, $name) {
			$accum .= '<p>' . $name . '</p>';
			return $accum;
		}, '');
		echo $output;
	}
	
} 