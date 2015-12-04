<?php
echo $this->Form->create($disposition);
echo $this->element('Disposition/Form/venues');
echo $this->element('Disposition/Form/storage');
echo $this->element('Disposition/Form/pieces');
echo $this->element('Disposition/Form/destination');
echo $this->element('Disposition/Form/review');
echo $this->element('Disposition/Form/documents');
echo $this->Form->button('Submit');
echo $this->Form->end();
?>
