<?php
/* @var \Cake\View\View $this */

$limit = $this->Paginator->limitControl(
    [2=>2,5=>5,10=>10,20=>20,50=>50,100=>100],
    null,
    ['name' => $pagingScope . '[limit]', 'label' => 'PerPg', 'class' => 'someClass']);

$this->Paginator->options(['model' => $pagingScope]);
echo $this->Html->tag('ul',
    $this->Paginator->prev('Previous')
    . $this->Paginator->numbers()
    . $this->Paginator->next('Next')
    . $limit,
    ['class' => 'menu']);
