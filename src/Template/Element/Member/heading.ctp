
<!-- Element/Member/heading.ctp -->
<?php
if($this->request->getParam('action') == 'create'):
    $q = [
        'controller' => 'members',
        '?' => [
            'member' => $member->id
        ]];
    ?>
    <h1>
        <?=$this->ArtStackTools->refineLink($q); ?>
        <?=h($member->name())?>
    </h1>
<?php
endif;
?>
<!-- END Element/Member/heading.ctp -->
