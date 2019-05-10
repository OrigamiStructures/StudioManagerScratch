<?php
$unique = [4,5,6,13,14,19];
$onePoem = [7,8,9,10,16,17];
$conversation = [11,18];
foreach ($artworks->load() as $artwork_index => $artwork){
	echo $this->Html->tag('h1',$artwork->rootID() . ' || ' . $artwork->title());
	echo $this->Html->para(null, $artwork->description());
	osd($artwork->series);
	$formats = $artwork->formats->load();
	
	foreach ($formats as $format) {
		$data = [
			'id' => $format->id,
			'user_id' => $format->user_id,
			'title' => $format->title,
			'description' => $format->description,
			'image_id' => $format->image_id,
			'edition_id' => $format->edition_id,
			'subscription_id' => $format->subscription_id,
			'assigned_piece_count' => $format->assigned_piece_count,
			'fluid_piece_count' => $format->fluid_piece_count,
			'collected_piece_count' => $format->collected_piece_count,
		];
//		osd($data);
	}
//	osd($artwork->formats->load());

}
