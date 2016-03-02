<!-- Element/Member/refine.ctp -->
<?php
    $mode = ($editing) ? 'refine' : 'review';
?>
<section class="member">
    <?= $this->element('Member/heading') ?>
    <div class="member_left">
        <section class="identity">
            <?= $this->element("Member/identity_$mode") ?>
        </section>
        <div class="contacts">
            <?= $this->element("Contact/$mode") ?>
        </div>
    </div>
    <div class="member_right">
        <div class="addresses">
            <?= $this->element("Address/$mode") ?>
        </div>
        <div class="groups">
            <?= $this->element("Group/$mode") ?>
        </div>
    </div>
    <div class="member_controls">
        <?= $this->element("Member/{$mode}_controls") ?>
    </div>
</section>
<!-- END Element/Member/refine.ctp -->
