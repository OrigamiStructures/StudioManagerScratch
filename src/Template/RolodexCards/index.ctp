<?php

//osd($rolodexCards);
$search = ($rolodexCards->accessArgs()->setLayer('identity'));
foreach($rolodexCards->all() as $id => $card) {
//	osd($card);//die;
//	osd($card->name());//die;
//	osd($card->memberships);die;
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
	echo "<h1>{$card->name()}</h1>";
	if ($card->isMember()) {
		$result = collection($card->memberships())->reduce(function($product, $entity){
			$product .= '<p>' . $entity->name() . '</p>';
            return $product;
		},'');
        echo $result;
	}
}