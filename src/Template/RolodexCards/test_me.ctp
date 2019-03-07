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
$val = new \App\Model\Lib\ValueSource('contact', 'label');
osd($val);

foreach (["Institution","Person"] as $type) :
	echo "<section class='$type'>\r";
	$member_type_to_type_match = $cards->accessArgs()
			->setLayer('member')
			->valueSource('member_type')
			->filterValue($type);
	$setMembers = $cards->load($member_type_to_type_match);
	foreach($setMembers as $member) :

		echo "<p>{$member->getName(LAST_FIRST)}</p>"; 
	
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
	
	$result[$card->getName()] = [
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


