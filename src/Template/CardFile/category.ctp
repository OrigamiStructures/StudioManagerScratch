<?php
use App\Model\Lib\Layer;
use App\Model\Entity\PersonCard;
use App\Model\Lib\ContextUser;
use App\Model\Lib\LayerAccessProcessor;
use App\View\AppView;
use Cake\Utility\Text;

/**
 * @var AppView $this
 * @var ContextUser $contextUser
 * @var Layer $localSupervision
 * @var PersonCard $personCard
 */

$members = '';
if ($personCard->hasMembers()) {
    $members =  "<p><strong>Members</strong>: "
        . Text::toList($personCard->getMembers()->toValueList('name')) . '</p>';
}

?>

<?=
    $this->Html->link('Mixed Cards', ['action' => 'index'])
    . ' | ' . $this->Html->link('Categories', ['action' => 'groups'])
?>

    <h1><?= $personCard->rootElement()->name() ?></h1>

<?php
$membershipList = count($personCard->getMemberships()) == 0
    ? 'None'
    : \Cake\Utility\Text::toList($personCard->getMemberships()->toValueList('name'));
echo "</p>";
echo '<p>Memberships: ' . $membershipList . '</p>';

echo $members;

