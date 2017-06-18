<?php 
namespace Conpoz\App\Controller;

class Message extends \Conpoz\App\Controller\BaseController
{
    public function sendAction ($bag)
    {
        $resultAry = $bag->net->httpGet('http://127.0.0.1:50126/send/' . $bag->req->getPost('message'));
        echo $resultAry['result'];
    }
}