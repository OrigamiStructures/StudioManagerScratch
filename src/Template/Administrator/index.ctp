<?php
echo $this->Form->create(null, ['action' => 'userDataIntegrity']);
echo $this->Form->select('users', $users);
echo $this->Form->submit();
echo $this->Form->end();
