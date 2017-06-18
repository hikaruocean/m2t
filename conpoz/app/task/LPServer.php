<?php 
namespace Conpoz\App\Task;

class LPServer
{
    public function runAction ($bag)
    {
        $port = 50126;
        $l = new \Conpoz\App\Lib\Server\Listener($port);
        $l->dispatch();
    }
}