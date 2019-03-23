<?php
foreach($rolodexCards->all() as $id => $card) {
	echo "<h1>{$card->name()}</h1>";
	if ($card->isMember()) {
		osd($card->distinct('name', 'memberships'), 'card distinct w/2 args');
//		osd($card->distinct('name'), 'card distinct w/1 arg');
		osd($card->find()
				->setValueSource('name')
				->setLayer('memberships')
				->loadDistinct(),
				'This is the modern call');
//		$result = collection($card->memberships())
//            ->reduce(function($product, $entity){
//                $product .= '<p>' . $entity->name() . '</p>';
//                return $product;
//            },'');
//        echo $result;
	}
} 