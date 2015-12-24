<!-- Element/Series/fieldset.ctp -->
<?php $this->request->data['Series'] = ['series' => $series];
osd($series);?>
<fieldset>
    <?= $this->Form->input('Series.id', ['empty' => 'select a series', 'label' => 'Series', 'type' => 'select']); ?>
</fieldset>
