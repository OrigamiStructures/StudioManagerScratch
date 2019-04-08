<?php

/*
 * Copyright 2015 Origami Structures
 */

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;

/**
 * CakePHP MemberHelper
 * @author jasont
 */
class MemberViewHelper extends Helper {
    
    public $helpers = ['Html'];
    
    protected $_classMap = [
        MEMBER_TYPE_INSTITUTION => 'institution',
        MEMBER_TYPE_PERSON => 'person',
        MEMBER_TYPE_USER => 'user',
        MEMBER_TYPE_CATEGORY => 'category',
    ];
    
    protected $_iconMap = [
        MEMBER_TYPE_INSTITUTION => ICON_MEMBER_TYPE_INSTITUTION,
        MEMBER_TYPE_PERSON => ICON_MEMBER_TYPE_PERSON,
        MEMBER_TYPE_USER => ICON_MEMBER_TYPE_USER,
        MEMBER_TYPE_CATEGORY => ICON_MEMBER_TYPE_CATEGORY,
    ];
    
    public function identifier($member) {
        $class = $this->_classMap[$member->member_type];
        $icon_type = $this->_iconMap[$member->member_type];
        $icon = $this->Html->tag('i', '', ['class' => $icon_type . ' inline_icon'] );
        return $this->Html->tag('span', $icon . h($member->name()), ['class' => $class]);
    }
}
