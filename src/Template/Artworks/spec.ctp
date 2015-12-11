<?php
//osd($artwork_element);//die;
//osd($this->viewVars);
//osd($editions);
?>
<?= $this->element("Artwork/{$artwork_element}"); ?>
<?= $this->element("Edition/{$edition_element}"); ?>
<?= $this->element("Format/{$format_element}"); ?>