<?php
/* @var \App\Model\Entity\ManifestStack $manifestStack */
/* @var \App\View\AppView $this */

echo $this->Html->link('Back to Supervisor page', $referer);

echo $this->Html->para('manager', 'Manager: ' . $manifestStack->managerCard()->name());
echo $this->Html->para('artist', 'Artist: ' . $manifestStack->artistCard()->name());

$permissions = $manifestStack->getLayer('permissions')->toArray();
if (count($permissions) > 0) {
    osd($permissions);
} else {
    echo $this->Html->para('', 'Full Access');
}
osd($manifestStack);
