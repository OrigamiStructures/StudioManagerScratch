<h1>Search Results</h1>

<h2>Artwork</h2>
<?php if (!empty($art)) : 
	
		foreach ($art as $artwork) :
			$this->set('artwork', $artwork);
			echo $this->element('Artwork/text') . "\n";
		endforeach;

	else :; 
	
		echo "<p>No artwork found for '{$this->request->data('search')};";
	
	endif; ?>

<h2>Members</h2>
<?php if (!empty($members)) : 
	
		foreach ($members as $member) :
			$this->set('member', $member);
			echo $this->element('member/text') . "\n";
		endforeach;

	else :; 
	
		echo "<p>No artwork found for '{$this->request->data('search')};";
	
	endif; ?>
