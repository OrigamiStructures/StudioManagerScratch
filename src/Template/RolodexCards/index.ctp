<?php

//osd($rolodexCards);
$search = ($rolodexCards->accessArgs()->setLayer('identity'));
foreach($rolodexCards->all() as $id => $card) {
	osd($card);die;
	osd($card->load()->setLlayer('memberships'));die;
//	$card->find('all')
//			->layer('memberships')
//			->filter('sourceValue', 'filterValue', 'condition')
//			->distinct('sourceValue')
//			->member('index')
//			->element('index')
//			->keyedValues('keyValue', 'sourceValue')
//			->select('value', 'value', 'value')
//			->paginate('limit', 'page')
//			->toAarray();
			
//	osd($card->distinct('memberships', 'name'));
//	osd($card->primaryEntity());
//	echo "<h1>{$card->name()}</h1>";
//	if ($card->hasMemberships()) {
//		collection($card->memberships->distinct())->map(function($entity){
//			echo '<p>' . $membership->name() . '</p>';
//		});
//	}
}