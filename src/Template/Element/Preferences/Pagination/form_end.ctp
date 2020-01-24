<?php
use App\Lib\Prefs;
use App\Lib\PrefCon;
/**
 * @var \App\View\AppView $this
 * @var Prefs $PrefsObject
 */

echo '</ul>';
echo $this->Form->control('id', ['type' => 'hidden']);
echo $this->Form->submit();
echo $this->Form->end();
