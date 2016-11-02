<h1>Search Results</h1>

<h2>Artwork</h2>
<?php if (!empty($artworks)) : 
	
		foreach ($artworks as $artwork) :
			$this->set('artwork', $artwork);
			echo $this->element('Artwork/text') . "\n";
			foreach ($artwork->editions as $edition) :

				// CLONE CODE FROM ELEMENT EDITION/FULL
				if (!is_null($edition->type)) { // CREATE doesn't know type
					$factory = $this->loadHelper('EditionFactory');
					$this->set('EditionHelper', $factory->load($edition->type));
				}
				
				$this->set('edition', $edition);
				echo $this->element('Edition/text') . "\n"; 
				
				/**
				 * DIPOSITION TOOLS ARE ON FORMAT/TEXT
				 * pieceTools() method on a helper?
				 */
			endforeach;
		endforeach;

	else :; 
	
		echo "<p>No artwork found for '{$this->request->data('search')};";
	
	endif; ?>

<h2>Members</h2>
<?php if (!empty($members)) : 
	
		foreach ($members as $member) :
			$this->set('member', $member);
			echo $this->element('member/text') . "\n";
			echo $this->element('member/controls') . "\n";
		endforeach;

	else :; 
	
		echo "<p>No artwork found for '{$this->request->data('search')};";
	
	endif; ?>
