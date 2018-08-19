<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * PieceAssignment is the user directed movement of existing pieces inside an edition
 * 
 * Allocation and deletion of pieces based on edition size changes are automated 
 * according to rules defined in another class. Assigment is controlled by input 
 * provided by the user as they explicitly move pieces to and from formats in 
 * the edition.
 * 
 * In reality, all this code is currently in App\Form\AssignmentForm. The final 
 * home class for the logic is up for debate. Notes about the options can 
 * be found in AssignmentForm.
 * 
 * @author dondrake
 */
class PieceAssignmentComponent extends Component {
	
}
