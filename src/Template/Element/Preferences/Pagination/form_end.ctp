<?php
use App\Lib\Prefs;
use App\Constants\PrefCon;
/**
 * @var \App\View\AppView $this
 */

echo '</ul>';
echo $this->Form->control('id', ['type' => 'hidden']);
echo $this->Form->submit();
echo $this->Form->end();
