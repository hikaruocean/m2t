<?php 
namespace Conpoz\App\Task;

class LPServer
{
    public function runAction ($bag)
    {
        $port = $this->app->argv[2];
        $l = new \Conpoz\App\Lib\LPServer\Listener(array('port' => $port, 'allowOrigin' => '*', 'keepAlive' => true, 'hashKey' => $bag->config->LPServer['hashKey'], 'centerHost' => '127.0.0.1', 'centerPort' => '50000'));
        // $l = new \Conpoz\App\Lib\LPServer\Listener(array('port' => $port, 'allowOrigin' => '*', 'keepAlive' => true));
        $l->dispatch();
    }
    
    public function centerAction ($bag)
    {
        $port = 50000;
        $l = new \Conpoz\App\Lib\LPCenter\Listener(array('port' => $port));
        $l->dispatch();
    }
}