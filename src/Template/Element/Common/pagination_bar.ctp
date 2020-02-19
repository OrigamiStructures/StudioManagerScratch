<?php
/* @var \Cake\View\View $this */

$this->Paginator->options(['model' => $pagingScope]);
echo $this->Html->tag('ul',
    $this->Paginator->prev('Previous')
    . $this->Paginator->numbers()
    . $this->Paginator->next('Next'),
    ['class' => 'menu']);

