
<!-- Element/Address/full.ctp -->
<section class="address">
    <div class="columns small-12 medium-9 description">
        <?php
        foreach ($addresses as $key => $address) {
            $this->set('address', $address);
            echo $this->element('Address/display');
        }
        ?>
    </div>
</section>
<!-- END Element/Address/full.ctp -->
