<!-- Element/Member/many.ctp -->
<?php foreach ($members as $member): ?>
    <?php $this->set('member', $member); ?>
    <section class="member_entry">
        <div class="name">
            <?= $this->element('Member/text'); ?>
        </div>
        <div class="controls">
            <?= $this->element('Member/controls'); ?>
        </div>
    </section>

<?php endforeach; ?>
<!-- END Element/Member/many.ctp -->
