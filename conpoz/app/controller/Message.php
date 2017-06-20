<?php 
namespace Conpoz\App\Controller;

class Message extends \Conpoz\App\Controller\BaseController
{
    public function sendAction ($bag)
    {
        $message = $bag->req->getPost('message');
        $payload = 'data=' . urlencode(json_encode(array('message' => $message))) . '&channel=' . urlencode(json_encode(array('global_message')));
        $resultAry = $bag->net->httpGet('http://127.0.0.1:50126/send?' . $payload);
        echo $resultAry['result'];
    }
}