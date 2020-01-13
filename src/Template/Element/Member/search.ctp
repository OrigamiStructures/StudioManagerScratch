<?php
/* @var \App\View\AppView $this */

echo $this->Form->create($memberSchema);
echo $this->Form->control('first_name');
echo $this->Form->control('fnMode', ['type' => 'radio']);
echo $this->Form->submit();
echo $this->Form->end();
