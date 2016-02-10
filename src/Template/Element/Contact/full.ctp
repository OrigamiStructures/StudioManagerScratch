
<!-- Element/Contact/full.ctp -->

<?php
if(!empty($contacts)):
?>
<section class="contact">
    <div class="columns small-12 medium-9 description">
        <?php
        foreach ($contacts as $key => $contact) {
            $this->set('contact', $contact);
            echo $this->element('Contact/display');
        }
        ?>
    </div>
</section>
<?php
endif;
?>
<!-- END Element/Contact/full.ctp -->
