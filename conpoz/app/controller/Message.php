<?php 
namespace Conpoz\App\Controller;

class Message extends \Conpoz\App\Controller\BaseController
{
    public function sendAction ($bag)
    {
        $message = $bag->req->getPost('message');
        $payload = 'data=' . urlencode(json_encode(array('message' => $message))) . '&target=' . urlencode(json_encode('*'));
        $resultAry = $bag->net->httpGet('http://127.0.0.1:50126/send?' . $payload);
        echo $resultAry['result'];
    }
}