<?php
//osd($artwork_element);//die;
//osd($this->viewVars);
//osd($edition_element);die;

if ($artwork_element) {
	echo $this->element("Artwork/{$artwork_element}");
}
echo $this->element("Edition/{$edition_element}"); 
echo $this->element("Format/{$format_element}"); 

?>