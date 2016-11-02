<table>
	<thead>
		<tr>
			<th>
				Edition
			</th>
			<?php $count = 0; while ($count < 100) : ?>
			<th><?= ++$count; ?></th>
			<?php endwhile; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($formats as $format) : ?>
		<tr>
			<td>
				<?= $format->edition->artwork->title?>
				<?php // $format->edition->identityLabel() . "<br />\n" . $format->identityLabel()?>
			</td>
			<?php foreach($format->pieces as $piece) : ?>
			<td>
				<?php 
				if ($piece->isCollected()) {
					echo '<p style="color: firebrick;">$</p>';
				} elseif (!$piece->isFluid()) {
					echo '<p>*</p>';
				} else {
					echo '&nbsp;';
				}
				?>
			</td>
			<?php endforeach; ?>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table><?php 
