<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * EditionStackComponent provides a unified interface for the three layers, Edition, Format and Piece
 * 
 * Managing Pieces within Editions and their Formats requires complex data 
 * objects and collections. This component localizes these processes and provides 
 * tools required by the three controllers as they collaborate to maintain 
 * edition content. The actual movement of Pieces across the Edition/Format 
 * layers is passed of to a separate component.
 * 
 * @author dondrake
 */
class EditionStackComponent extends Component {
	
	public function stackQuery() {
		
	}
}
