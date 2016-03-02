
<!-- Element/Member/heading.ctp -->
<?php
if($SystemState->now() != MEMBER_CREATE):
    $q = [
        'controller' => 'members', 
        '?' => [
            'member' => $member->id
        ]];
    ?>
    <h1>
        <?=$this->InlineTools->refineLink($q); ?>
        <?=h($member->name)?>
    </h1>
<?php
endif;
?>
<!-- END Element/Member/heading.ctp -->
