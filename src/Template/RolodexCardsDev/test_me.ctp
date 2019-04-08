<?php
use Cake\Collection\Collection;
?>

<style>
	.tests {
		display: grid;
		grid-template-columns: 1fr 1fr;
		grid-template-areas: "Institution Person";
	}
	label {
		display: block;
	}
</style>
<div class="tests">
<?php

	/**
	 * CALLABLE EXPERIMENT
	 */
//	$vals = [1,2,3,4,5,6,7,8,9,10];
//	$collection = collection($vals);
//	$x1 = '33'; $x2 = '11';
//	$result = $collection->reduce(function($accum, $value, $index) use ($x1, $x2){
//		return callThis($accum, $value, $index, $x1, $x2);
//	}, '');
//	osd($result);
//	
//	function callThis ($accum, $value, $index, $x1, $x2) {
//		osd(func_get_args());
//		return $accum .= "-$value-$x1-$x2";
//	}
	
	
$val = new \App\Model\Lib\ValueSource('contact', 'label');
osd($val);

foreach (["Institution","Person"] as $type) :
	echo "<section class='$type'>\r";
	$member_type_to_type_match = $cards->accessArgs()
			->setLayer('member')->specifyFilter('member_type', $type);
	$setMembers = $cards->load($member_type_to_type_match);
	foreach($setMembers as $member) :

		echo "<p>{$member->name(LAST_FIRST)}</p>"; 
	
	endforeach;
	echo "</section>\r";
endforeach;
?>
</div>

<?php

$result = [];
foreach($cards->all() as $card) {
//	
//	
	$secondaryA = [];
	foreach($card->getSecondary(ADDRESS) as $address) {
		$secondaryA[] = $address->asString();
	}
	$secondaryC = [];
	foreach($card->getSecondary(CONTACT) as $contact) {
		$secondaryC[] = $contact->asString();
	}
	
	$result[$card->name()] = [
		'Primary Address' => [
			$card->hasPrimary(ADDRESS) ? 
			$card->getPrimary(ADDRESS, BARE)->asString() : 
			'No primary address'
		],
		'Primary Contact' => [
			$card->hasPrimary(CONTACT) ? 
			$card->getPrimary(CONTACT, BARE)->asString() : 
			'No primary contact'
		],
		'Secondary Addresses' => $secondaryA,
		'Secondary Contacts' => $secondaryC,
	];
		
}
	echo $this->Html->nestedList($result);
	
	$options = [];
	$allAddressesArg = $cards->accessArgs()
			->setLimit('all')
			->setLayer('addresses');
	foreach($cards->load($allAddressesArg) as $address) {
		$options[$address->id] = " {$address->asString()}";
	}
	
//	
//	echo $this->Form->radio('address', $options);
//	echo $this->Form->select('address', $options);
//	

//	osd($card->getPrimary(ADDRESS), 'PRIMARY ADDRESS ' . $card->getName());
//	osd($card->getSecondary(ADDRESS), 'SECONDARY ADDRESS ' . $card->getName());
//	
//	osd($card->getPrimary(CONTACT), 'PRIMARY CONTACT ' . $card->getName());
//	osd($card->getSecondary(CONTACT), 'SECONDARY CONTACT ' . $card->getName());
//	osd($card->getPrimary('badVal'));


