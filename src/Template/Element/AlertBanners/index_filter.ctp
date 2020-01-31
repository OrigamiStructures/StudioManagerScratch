<?php
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
