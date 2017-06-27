<?php 
namespace Conpoz\App\Task;

class LPServer
{
    public function runAction ($bag)
    {
        $port = 50126;
        $l = new \Conpoz\App\Lib\Server\Listener(array('port' => '50126', 'allowOrigin' => '*', 'keepAlive' => true, 'hashKey' => $bag->config->LPServer['hashKey']));
        $l->dispatch();
    }
}