<?php
use App\Model\Lib\Layer;
use App\Model\Entity\PersonCard;
use App\Model\Lib\ContextUser;
use App\Model\Lib\LayerAccessProcessor;
use App\View\AppView;

/**
 * @var AppView $this
 * @var ContextUser $contextUser
 * @var Layer $localSupervision
 * @var PersonCard $personCard
 */


$con_add_format = '</br><span id="%s%s">%s</span>';

?>

<?= $this->Html->link('Index page', ['action' => 'index']) ?>

    <h1><?= $personCard->rootElement()->name() ?></h1>


<?php
$membershipList = count($personCard->getMemberships()) == 0
    ? 'None'
    : \Cake\Utility\Text::toList($personCard->getMemberships()->toValueList('name'));
echo "</p>";
echo '<p>Memberships: ' . $membershipList . '</p>';

