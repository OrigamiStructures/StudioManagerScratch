<?php
$f = [
	[
		1, 
		'title' => '', 
		'description' => 'Watercolor 6 x 15"'
	],
	[
		2, 
		'title' => 'Card Bound', 
		'description' => 'Digital output with cloth-covered card stock covers'
	],
	[
		3, 
		'title' => 'Mini Box', 
		'description' => 'Paper covered container with 4 trays. Trays display '
		. 'mounted and lacquered digital content on the front and QR codes '
		. 'which link web addresses on the reverse'
	],
	[
		4, 
		'title' => '', 
		'description' => 'Prototype made while developing edition details. '
		. 'Paper covered container with 4 trays. Trays display mounted and '
		. 'lacquered digital content on the front and QR codes which link '
		. 'web addresses on the reverse.'
	],
	[
		5, 
		'title' => '16 x 20 Box', 
		'description' => '16 x 20 drop-spine box with linen sides, brown iris '
		. 'case. Leather label (honey calf) on front with title stamped in '
		. 'black. Title stamped in black of cloth of spine'
	],
	[
		6,
		'title' => 'Hand bound', 
		'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet '
		. 'stitched, bound in cloth over board covers.'
	],
	[
		7,
		'title' => '', 
		'description' => '6.5" x 5.25" 20 page offset printed book. '
		. 'Pamphlet stitched, in a paper cover with sleaves'
	],
	[
		8,
		'title' => '', 
		'description' => '6.5" x 5.25" 20 page offset printed book '
		. 'unbound page sets'
	],
	[
		9,
		'title' => '', 
		'description' => 'Digital printing on Mohawk Superfine; sewn signatures '
		. 'bound in black goat skin. Pages have graphite edging. '
		. 'Covers are gold foil stamped.'
	], 
	[
		10,
		'title' => 'Boxed', 
		'description' => ''
	],
	[
		11,
		'title' => '', 
		'description' => ''
	],
	[
		12,
		'title' => '16 x 20', 
		'description' => 'Loose silver gelatin prints'
	],
	[
		13,
		'title' => '8x10 ', 
		'description' => 'Matted, framed, silver gelatin prints.'
	],
	[
		14,
		'title' => '', 
		'description' => ''
	],
	[
		15,
		'title' => '', 
		'description' =>'6.5" x 5.25" 20 page offset printed book. '
		. 'Pamphlet stitched, bound in cloth over board covers.'
	]
];
$unique = [4,5,6,13,14,19];
$onePoem = [7,8,9,10,16,17];
$conversation = [11,18];

$pieces = [];
foreach ($artworks->load() as $artwork){
//	osd($artwork->series);
	if ($artwork->series->hasElements()) {
		osd($artwork->series->distinct('title'));
	}
	echo $this->Html->tag('h1',$artwork->rootID() . ' || ' . $artwork->title());
	echo $this->Html->para(null, $artwork->description());
	osd($artwork->editions->IDs(), 'editions');
	
	$formats = $artwork->formats->load();
	
	// each format
	// write the editions_formats join records
	// find piece on original id and change to new format_id
	// hand-create the new format records from $f array below
	foreach ($formats as $format) {
		$batch = $artwork
				->find()
				->setLayer('pieces')
				->specifyFilter('format_id', $format->id)
				->load();
		foreach ($batch as $linked_piece) {
			echo $this->Html->para(NULL, describeOriginal($artwork, $format, $linked_piece));
			$linked_piece->format_id = $format->range_flag;
			echo $this->Html->para(NULL, describeNew($artwork, $f, $linked_piece));
		}
		$pieces += $batch;
		unset($batch);
	}
//	osd($artwork->formats->load());
//	//edition 23 is unused
	//format 24 is unsued
}
$edition_id_set = $artworks->find()
		->setLayer('editions')
		->setValueSource('id')
		->loadDistinct();
sort($edition_id_set);
//osd($edition_id_set);

function describeOriginal($art, $format, $piece) {
	$pattern = '%s #%s/q%s %s:%s';
	return sprintf(
			$pattern, 
			$art->title(), 
			$piece->number, 
			$piece->quantity,
			$piece->format_id,
			$format->description
		); 
}

function describeNew($art, $format, $piece) {
	$pattern = '%s #%s/q%s %s:%s';
	return sprintf(
			$pattern, 
			$art->title(), 
			$piece->number, 
			$piece->quantity, 
			$piece->format_id - 1,
			$format[$piece->format_id - 1]['description']
		); 
}

