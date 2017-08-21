<?php
namespace Conpoz\App\Controller;

class Landing extends \Conpoz\App\Controller\BaseController
{
    public function indexAction ($bag)
    {
        $RA = $bag->tool->force($_COOKIE, 'randomAccount');
        if (!is_null($RA)) {
            $rh = $bag->dbquery->execute("SELECT id, name, channel, user_role, account FROM user WHERE random_account = :RA ", array('RA' =>$RA));
            $obj = $rh->fetch();
            if ($obj) {
                $bag->sess->user_id = (int) $obj->id;
                $bag->sess->channel = $obj->channel;
                $bag->sess->name = $obj->name;
                $bag->sess->user_role = $obj->user_role;
                $bag->sess->account = $obj->account;
                setcookie('randomAccout', $RA, time()+60*60*24*365, '/', null, false, true);
                header('Location: /');
            }
        }
        $ruleObj = new \Conpoz\App\Lib\ValidateRule\Register();
        $this->view->addView('/htmlTemplate');
        $this->view->addView('/landing/index');
        require($this->view->getView());
    }
    
    public function getPassportAction ($bag)
    {
        $randomToken = md5(microtime(true));
        $rh = $bag->dbquery->insert('user', array('random_account' => $randomToken, 'user_role' => 'member'));
        $id = $rh->lastInsertId();
        $bag->dbquery->update('user', array('channel' => $id), "id = :id", array('id' => (int) $id));
        $bag->sess->channel = $bag->sess->user_id = $id;
        $bag->sess->name = 'Guset';
        $bag->sess->user_role = 'member';
        $bag->sess->account = null;
        setcookie('randomAccount', $randomToken, time()+60*60*24*365, '/', null, false, true);
        header('Location: /');
    }
    
    public function logoutAction ($bag) 
    {
        $bag->sess->truncate();
        setcookie('randomAccount', null, time() - 1, '/', null, false, true);
        header('Location: /');
    }
    
    public function loginAction ($bag)
    {
        $data = $bag->req->getPost(array('account', 'password'));
        $ruleObj = new \Conpoz\App\Lib\ValidateRule\Register();
        $ruleObj->autoChoice($data);
        $errMsg = $bag->validator->valid($ruleObj, $data);
        if (!empty($errMsg)) {
            echo json_encode(array('result' => -1, 'message' => implode(',', $errMsg)));
            return;
        }
        $rh = $bag->dbquery->execute("SELECT id, name, channel, user_role, account FROM user WHERE account = :account AND password = :password ", array('account' => $data['account'], 'password' => md5($data['password'])));
        $obj = $rh->fetch();
        if (!$obj) {
            echo json_encode(array('result' => -1, 'message' => '帳號或密碼錯誤'));
            return;
        }
        $bag->sess->user_id = $obj->id;
        $bag->sess->channel = $obj->channel;
        $bag->sess->name = $obj->name;
        $bag->sess->user_role = $obj->user_role;
        $bag->sess->account = $obj->account;
        
        $randomToken = md5(microtime(true));
        $bag->dbquery->update('user', array('random_account' => $randomToken), "id = :id", array('id' => $obj->id));
        setcookie('randomAccount', $randomToken, time()+60*60*24*365, '/', null, false, true);
        echo json_encode(array('result' => 0));
        return;
    }
}