<?php
foreach($rolodexCards->all() as $id => $card) {
	echo "<h1>{$card->name()}</h1>";
	if ($card->isMember()) {
		$result = collection($card->memberships())->reduce(function($product, $entity){
            $product .= '<p>' . $entity->name() . '</p>';
            return $product;
		},'');
        echo $result;
	}
}