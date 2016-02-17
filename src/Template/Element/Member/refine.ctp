<!-- Element/Member/refine.ctp -->
<section class="member">
    <section class="identity">
        <?= $this->element('Member/text') ?>
        <?= $this->element('Member/fieldset') ?>
    </section>
    <div class="contacts">
        <?= $this->element('Contact/refine') ?>
    </div>
    <div class="addresses">
        <?= $this->element('Address/refine') ?>
    </div>
    <div class="groups">
        <?= $this->element('Group/refine') ?>
    </div>
</section>
<!-- END Element/Member/refine.ctp -->
