<!-- Element/Format/image.ctp -->
						<?= // THIS IS NOT RIGHT!!!
						$this->Html->image(
								"editions.$edition_index.formats.$format_index.image" == NULL ?
								"NoImage.png" : $format->image->fullPath); ?>

						<!-- END Element/Format/image.ctp -->
