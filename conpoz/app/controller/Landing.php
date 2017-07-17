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
                setcookie('randomAccout', $randomToken, time()+60*60*24*365, '/', null, false, true);
                header('Location: /');
            }
        }
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
}