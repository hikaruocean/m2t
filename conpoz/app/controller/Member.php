<?php 
namespace Conpoz\App\Controller;

class Member extends \Conpoz\App\Controller\BaseController
{
    public function registerAction($bag) {
        try {
                
            $data = $bag->req->getPost(array('account', 'password', 'retype_password', 'name', 'channel'));
            $ruleObj = new \Conpoz\App\Lib\ValidateRule\Register();
            $ruleObj->setRuleErrBreak(true)->autoChoice($data);
            $errAry = $bag->validator->valid($ruleObj, $data);
            if (!empty($errAry)) {
                throw new \Exception($errAry[0]);
            }
            $data['password'] = md5($data['password']);
            unset($data['retype_password']);
            $bag->dbquery->update('user', $data, "id = :id", array('id' => $bag->sess->user_id));
            $bag->sess->channel = $data['channel'];
            $bag->sess->name = $data['name'];
            $bag->sess->account = $data['account'];
            echo json_encode(array('result' => 0));
        } catch (\Exception $e) {
            echo json_encode(array('result' => -1, 'message' => $e->getMessage()));
        }
    }
    
    public function registerFormAction ($bag)
    {
        $ruleObj = new \Conpoz\App\Lib\ValidateRule\Register();
        $this->view->addView('/member/registerForm');
        require($this->view->getView());
    }
}