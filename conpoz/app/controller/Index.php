<?php
namespace Conpoz\App\Controller;

class Index
{
    public function indexAction ($bag)
    {
        /**
        * use service bag
        */
        var_dump($bag->req->getQuery(array('name', 'go', 'sex')));

        /**
        * use static getService
        */
        $req = \Conpoz\Core\Lib\Util\Container::getService('req');
        var_dump($req->getQuery(array('name', 'go', 'sex')));
        /**
         *  version 1
         *  query db in controller
         */
        // $rh = $bag->dbquery->execute("SELECT * FROM questions WHERE 1");

        /**
        * version 2
        * query db by call model (use magic function __get())
        */
        // $rh = $this->model->Questions->getListRh();

        /**
         *  version 3
         *  query db by call model (use loader function load($modelName, $contructData = null))
         *  this version can call model's contruct function
         */
        $qModel = $this->model->load('Questions');
        $rh = $qModel->getListRh();

        /**
         * render view
         */
        $this->view->addView('/htmlTemplate');
        $this->view->addView('/index/index');
        require($this->view->getView());
    }
    
    public function curl2Action ($bag)
    {
        $returnAry = $bag->net->httpGet('https://www.youtube.com/watch?v=ZFo8-JqzSCM');
        preg_match('/<title>(.*)\s\-.*<\/title>/',$returnAry['result'], $match);
        var_dump($match);
    }

    public function uploadAction () {
        if ($this->bag->req->getMethod() == 'POST') {
            $fileObjAry = $this->bag->req->getFile('image');

            if (empty($fileObjAry[0]->name)) {
                return;
            }
            $image = $this->bag->imgMng->make($fileObjAry[0]->tmpName)->autoOrient();
            $image->fitToWidth(600);
            echo $image->output();
        } else {
            $this->view->addView('/htmlTemplate');
            $this->view->addView('/index/upload');
            require($this->view->getView());
        }
    }

    public function sessionAction ($bag)
    {
        $bag->sess;
        echo '$_SESSION<br />';
        var_dump($_SESSION);
        echo '<br />';
        echo '$bag->sess<br />';
        var_dump($bag->sess);
        $bag->sess->begin();
        $bag->sess->list[] = rand(0, 9);
        echo '<br />';
        echo '$bag->sess change to<br />';
        var_dump($bag->sess);
        $bag->sess->truncate();
        $bag->sess->commit();
        $bag->sess->begin();
        $bag->sess->go = 'go' . rand(0, 9);
        $bag->sess->commit();
        $bag->sess->begin();
        $bag->sess->tt = 'tt';
        $bag->sess->rollback();
        echo '<br />';
        $bag->sess->import(array(1 => 'a', 2 => 'b'));
        var_dump($bag->sess->export());
    }

    public function curlAction ()
    {
        $content = $this->bag->net->httpRequest('GET', 'http://www.onlypet.com.tw');
        var_dump($content);
    }

    public function validateAction($bag)
    {
        $validateRule = new \Conpoz\App\Lib\ValidateRule\Test();

        if ($bag->req->getMethod() != 'POST') {
            $this->view->addView('/htmlTemplate');
            $this->view->addView('/index/validate');
            require($this->view->getView());
        } else {
            // $post = $bag->req->getPost(array('value1', 'value2', 'value3'));
            $errAry = $bag->validator->valid($validateRule, $_POST);
            if (!empty($errAry)) {
                var_dump($errAry);
                return;
            }
            echo 'matched!';
        }
    }

    public function delAction ($bag)
    {
        $rh = $bag->dbquery->delete(array('t1', 't2'), "t1.id = t2.t1_id AND t1.id = :id", array('id' => 1));
        if (!$rh->success()) {
            var_dump($rh);
        } else {
            echo 'success';
        }
    }

    public function updateAction($bag)
    {
        $rh = $bag->dbquery->update(array('t1', 't2'), array('t1.name' => 'zzz', 't2.name' => 'xxx'), "t1.id = t2.t1_id AND t1.id = :id", array('id' => 2));
        if (!$rh->success()) {
            var_dump($rh);
        } else {
            echo 'success';
        }
    }

}
