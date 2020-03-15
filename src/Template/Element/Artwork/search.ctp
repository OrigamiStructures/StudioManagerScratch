<?php
/* @var \App\View\AppView $this */

echo $this->Form->create(null);
echo $this->Form->control('title');
echo $this->Form->control('title_mode',
    ['options' => $modes, 'type' => 'radio', 'default' => 3]);
echo $this->Form->control('description');
echo $this->Form->control('description_mode',
    ['options' => $modes, 'type' => 'radio', 'default' => 3]);
echo $this->Form->submit();
echo $this->Form->end();
