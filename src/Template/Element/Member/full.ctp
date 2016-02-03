<!-- Element/Member/full.ctp -->
<?php foreach ($members as $member): ?>
    <?php $this->set('member', $member); ?>
<?php
osd($member, 'Main member');
foreach ($member_groups as $key => $group) {
    echo $this->Html->para('groups', "$group->id --- $group->displayTitle");
}
//osd($member_groups->toArray(), 'found groups');
?>
    <div class="member row">
        <div class="columns small-12 medium-9 text">
            <?= $this->element('Member/text'); ?>
            <section class="addresses">
            <?php
                $this->set('addresses', $member->addresses);
                echo $this->element('Address/full');
            ?>
            </section>
            <section class="contacts">
            <?php
                $this->set('contacts', $member->contacts);
                echo $this->element('Contact/full');
            ?>
            </section>
        </div>
    </div>
    <div class="control row">
        <?= $this->element('Member/controls'); ?>
    </div>
<?php endforeach;?>
<!-- END Element/Member/full.ctp -->
