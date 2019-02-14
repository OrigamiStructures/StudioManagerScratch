<style>
	.tests {
		display: grid;
		grid-template-columns: 1fr 1fr;
		grid-template-areas: "Institution Person";
	}
</style>
<div class="tests">
<?php
foreach (["Institution","Person"] as $type) :
	echo "<section class='$type'>\r";
	$setMembers = $cards->load('member',['member_type', $type]);
	foreach($setMembers as $member) :

		echo "<p>{$member->getName(LAST_FIRST)}</p>"; 
	
	endforeach;
	echo "</section>\r";
endforeach;
?>
</div>

<?php

foreach($cards->all() as $card) {
	osd($card->getPrimary(ADDRESS), 'PRIMARY ADDRESS ' . $card->getName());
	osd($card->getSecondary(ADDRESS), 'SECONDARY ADDRESS ' . $card->getName());
	
	osd($card->getPrimary(CONTACT), 'PRIMARY CONTACT ' . $card->getName());
	osd($card->getSecondary(CONTACT), 'SECONDARY CONTACT ' . $card->getName());
	osd($card->getPrimary('badVal'));

}

