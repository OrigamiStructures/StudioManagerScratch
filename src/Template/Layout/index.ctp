<?php
/* @var \App\View\AppView $this */
$this->extend('/Layout/default');

/**
 * Replace with system to report page content filters to user
 * and to allow clearing of filter.
 */
if (!is_null($this->getRequest()->getSession()->read('filter'))) {
    $conditions = collection($this->getRequest()->getSession()->read('filter.conditions.OR'));
    $msg = $conditions->reduce(function($accum, $filter, $field) {
        $accum[] = "$field $filter";
        return $accum;
    }, []);
    $clearLink = $this->Html->link('[Clear] ', ['action' => 'clearFilter']);
    echo $this->Html->para('alias warning',
        $clearLink . '!* Page contents are filtered: ' . implode(' or ', $msg) . ' *!'
    );

    }

echo $this->Html->tag('ul',
    $this->Paginator->prev() . '<li>||</li>' . $this->Paginator->next(),
    ['class' => 'menu']);

echo $this->fetch('pagination_prefs_form');

echo $this->fetch('content');

//add search tools
echo '<h1>Search Tools Here</h1>';
