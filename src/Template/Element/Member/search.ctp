<?php
/* @var \App\View\AppView $this */

echo $this->Form->create($identitySchema);
echo $this->Form->control('first_name');
echo $this->Form->control('first_name_mode',
    ['options' => $identitySchema->modes, 'type' => 'radio', 'default' => 3]);
echo $this->Form->control('last_name');
echo $this->Form->control('last_name_mode',
    ['options' => $identitySchema->modes, 'type' => 'radio', 'default' => 3]);
echo $this->Form->submit();
echo $this->Form->end();
