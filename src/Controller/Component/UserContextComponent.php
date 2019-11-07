<?php
namespace App\Controller\Component;

use App\Model\Lib\ContextUser;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Response;

/**
 * UserContextComponent
 *
 * This class will verify that the proper context exists for
 * controller actions to run. If the context is insufficient:
 *
 * - The current uri will be recorded (session? cookie?)
 * - An instruction for the user will be prepared
 * - The user will be redirected to a page so they can establish proper context
 * - The page will gain tools in addition to its normal set. These tools
 *      will take the user back to thier original task. (the recorded uri)
 *
 * @todo 'required' data points should be mapped to conroller/action so
 *      redirects can be handled
 *
 * @link http://localhost/OStructures/article/usercontextcomponent
 * @package App\Controller\Component
 */
class UserContextComponent extends Component
{
    /**
     * Constructor
     *
     * @param \Cake\Controller\ComponentRegistry $registry A ComponentRegistry this component can use to lazy load its components
     * @param array $config Array of configuration settings.
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    /**
     * @param $data array list of the required points of context
     * @return true|Response Can proceed or will redirect
     */
    public function required($data)
    {
        $contextUser = $this->getController()->contextUser();
        $ok = TRUE;
        $toDefine = [];
        foreach ($data as $datum) {
            if (!$contextUser->has($datum)) {
                $toDefine[] = $datum;
                $ok = FALSE;
            }
        }
        if (!$ok) {
            $result = $this->getController()->redirect('rolodexcards');
            $this->getController()->Flash->error('You must select an artist');
        } else {
            $result = $ok;
        }

        return $result;
    }
}
