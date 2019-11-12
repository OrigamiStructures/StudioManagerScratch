<?php if (stristr($this->request->getParam('action'), 'create')) : ?>

		<?= $this->Form->input("editions.$edition_index.quantity", [
			'default' => 1,
			'label' => 'Edition Size'
		]); ?>

<?php else : ?>

		<?= $this->EditionFactory->concrete($edtion->type)->quantityInput($edition, $edition_index); ?>

<?php endif; ?>

