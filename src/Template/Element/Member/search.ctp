<?php
/* @var \App\View\AppView $this */

/* A stub for for REPL */

echo $this->Form->create(null);
echo '<fieldset>';
echo $this->Html->para(null, 'Filter the set of cards shown');
echo '<div style="display: inline-block; width: 49%;">';
echo $this->Form->control('first_name');
echo $this->Form->control('first_name_mode',
    ['options' => $identitySchema->modes, 'type' => 'radio', 'default' => 3]);
echo '</div>';
echo '<div style="display: inline-block; margin-left: 1rem;">';
echo $this->Form->control('last_name');
echo $this->Form->control('last_name_mode',
    ['options' => $identitySchema->modes, 'type' => 'radio', 'default' => 3]);
echo '</div>';
echo $this->Form->submit();
echo '</fieldset>';
echo $this->Form->end();
