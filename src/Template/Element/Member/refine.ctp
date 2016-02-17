<!-- Element/Member/refine.ctp -->
<section class="member">
    <?= $this->element('Member/text') ?>
    <div class="member_left">
        <section class="identity">
            <?= $this->element('Member/fieldset') ?>
        </section>
        <div class="contacts">
            <?= $this->element('Contact/refine') ?>
        </div>
    </div>
    <div class="member_right">
        <div class="addresses">
            <?= $this->element('Address/refine') ?>
        </div>
        <div class="groups">
            <?= $this->element('Group/refine') ?>
        </div>
    </div>
    <div class="controls">
        <?= $this->element('Member/form_controls') ?>
    </div>
</section>
<!-- END Element/Member/refine.ctp -->
