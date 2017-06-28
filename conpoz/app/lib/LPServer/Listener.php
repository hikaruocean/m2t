<?php
namespace Conpoz\App\Lib\LPServer;

class Listener 
{
    public $base,
        $listener,
        $socket;
    public $conn = array();
    public $channel = array();
    public $header404 = '';
    public $header200 = '';
    public $keepAlive;
    public $hashKey;
    public $HEL = "\r\n";
    public $centerHost;
    public $centerPort;
    public $centerConn;

    public function __destruct () 
    {
        foreach ($this->conn as &$c) $c = NULL;
    }

    public function __construct ($params) 
    {
        /**
        * $params['port'] = 50126
        * $params['allowOrigin'] = '*'
        */
        $params = array_merge(array('port' => '50126', 'allowOrigin' => '*', 'keepAlive' => false, 'hashKey' => null, 'centerHost' => null, 'centerPort' => '50126'), $params);
        $this->keepAlive = $params['keepAlive'];
        $this->hashKey = $params['hashKey'];
        $this->centerHost = $params['centerHost'];
        $this->centerPort = $params['centerPort'];
        /**
        * 啟用 cluster，對 center 進行 tcp 連線
        */
        if (!is_null($this->centerHost)) {
            $this->centerConn = $this->connectToCenter();
        }
        if ($this->keepAlive === true) {
            $connectionHeader = 'Connection: keep-alive';
        } else {
            $connectionHeader = 'Connection: close';
        }
        $this->header404 = 'HTTP/1.1 404 NOT FOUND' . PHP_EOL .
                    'Server: LPServer/1.0.0' . PHP_EOL .
                    'Content-Type: text/html; charset=utf-8'  . PHP_EOL .
                    'Transfer-Encoding: chunked' . PHP_EOL .
                    'Connection: close' . PHP_EOL . 
                    'Access-Control-Allow-Origin: ' . $params['allowOrigin'] . PHP_EOL . PHP_EOL;
        $this->header200 = 'HTTP/1.1 200 OK' . PHP_EOL .
                    'Server: LPServer/1.0.0' . PHP_EOL .
                    'Content-Type: application/json'  . PHP_EOL .
                    'Transfer-Encoding: chunked' . PHP_EOL .
                    $connectionHeader . PHP_EOL . 
                    'Access-Control-Allow-Origin: ' . $params['allowOrigin'] . PHP_EOL . PHP_EOL;
        
        
        $this->base = new \EventBase();
        if (!$this->base) {
            echo "Couldn't open event base";
            exit(1);
        }

        // Variant #1
        /*
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!socket_bind($this->socket, '0.0.0.0', $port)) {
            echo "Unable to bind socket\n";
            exit(1);
        }
        $this->listener = new EventListener($this->base,
            array($this, "acceptConnCallback"), $this->base,
            EventListener::OPT_CLOSE_ON_FREE | EventListener::OPT_REUSEABLE,
            -1, $this->socket);
         */

        // Variant #2
         $this->listener = new \EventListener($this->base,
             array($this, "acceptConnCallback"), $this->base,
             \EventListener::OPT_CLOSE_ON_FREE | \EventListener::OPT_REUSEABLE, -1,
             "0.0.0.0:" . $params['port']);

        if (!$this->listener) {
            echo "Couldn't create listener";
            exit(1);
        }

        $this->listener->setErrorCallback(array($this, "accept_error_cb"));
    }

    public function dispatch () 
    {
        $this->base->dispatch();
    }

    // This callback is invoked when there is data to read on $bev
    public function acceptConnCallback ($listener, $fd, $address, $ctx) 
    {
        // We got a new connection! Set up a bufferevent for it. */
        echo 'accept: ' . $fd . PHP_EOL;
        // $e = new \Event($this->base, $fd, \Event::TIMEOUT, function($fd, $what, $e) {
        //     var_dump($fd, $what, $e);
        //     echo "0.4 seconds elapsed";
        //     // By calling free() we prevent segmentation fault with
        //     // MALLOC_PERTURB_=$(($RANDOM % 255 + 1)), since otherwise the refcount for
        //     // Event will be bigger than the refcount for EventBase, and EventBase is destroyed earlier.
        //     // $e->free();
        // });
        // $e->data = $e;
        // $e->addTimer(0.4);
        $eTimestamp = time() + 60;
        $e = \Event::Timer($this->base, function ($data) use (&$eTimestamp, $fd, &$e) {
            $readyAddBuffer = array();
            $channel = $this->conn[$fd]->channel;
            foreach ($channel as $channelId) {
                
                $resultAry = array();
                if (!empty($this->channel[$channelId]['tempBuffer'])) {
                    /**
                     * 匯整 channel 的 send data 
                     */
                    while (($data = array_shift($this->channel[$channelId]['tempBuffer'])) && !is_null($data)) {
                        // var_dump($data);
                        $resultAry[]= $data;
                    }
                    /**
                     * 把 channel 的所有 fd 累加上 send data
                     */
                    foreach ($this->channel[$channelId]['conn'] as $rfd => $conn) {
                        if (!isset($readyAddBuffer[$rfd])) {
                            $readyAddBuffer[$rfd] = $resultAry;
                        } else {
                            $readyAddBuffer[$rfd] = array_merge($readyAddBuffer[$rfd], $resultAry);
                        }
                    }
                }
            }
            if (!empty($readyAddBuffer)) {
                foreach ($readyAddBuffer as $rfd => &$resultAry) {
                    $eb = new \EventBuffer();
                    $payloadAry = array('result' => 0, 'data' => $resultAry, 'smt' => microtime(true));
                    if (!is_null($this->hashKey)) {
                        $payloadAry['ts'] = time();
                        $payloadAry['tk'] = md5(json_encode($this->conn[$rfd]->channel) . $payloadAry['ts'] . $this->hashKey);
                    }
                    $payload = json_encode($payloadAry);
                    $eb->add($this->header200 . base_convert(strlen($payload), 10, 16) . $this->HEL . $payload . $this->HEL . '0' . $this->HEL . $this->HEL);
                    $this->conn[$rfd]->bev->output->addBuffer($eb);
                }
                unset($resultAry);
            }
            
            if ($this->keepAlive === false) {
                if ($eTimestamp > time()) {
                    $e->addTimer(0.5);
                    return;
                }
                $eb = new \EventBuffer();
                $payloadAry = array('result' => -1, 'smt' => microtime(true));
                if (!is_null($this->hashKey)) {
                    $payloadAry['ts'] = time();
                    $payloadAry['tk'] = md5(json_encode($this->conn[$fd]->channel) . $payloadAry['ts'] . $this->hashKey);
                }
                $payload = json_encode($payloadAry);
                $eb->add($this->header200 . base_convert(strlen($payload), 10, 16) . $this->HEL . $payload . $this->HEL . '0' . $this->HEL . $this->HEL);
                $this->conn[$fd]->bev->output->addBuffer($eb);
                return;
            }
            
            if ($eTimestamp <= time()) {
                $eTimestamp = time() + 60;
                $eb = new \EventBuffer();
                $payloadAry = array('result' => -1, 'smt' => microtime(true));
                if (!is_null($this->hashKey)) {
                    $payloadAry['ts'] = time();
                    $payloadAry['tk'] = md5(json_encode($this->conn[$fd]->channel) . $payloadAry['ts'] . $this->hashKey);
                }
                $payload = json_encode($payloadAry);
                $eb->add($this->header200 . base_convert(strlen($payload), 10, 16) . $this->HEL . $payload . $this->HEL . '0' . $this->HEL . $this->HEL);
                $this->conn[$fd]->bev->output->addBuffer($eb);
            }
            $e->addTimer(0.5);
        });
        $e->data = $e;
        $e->addTimer(0.1);
        
        $base = $this->base;
        $this->conn[$fd] = new \Conpoz\App\Lib\LPServer\ListenerConnection($base, $fd, $e, $this);
        
    }

    public function accept_error_cb ($listener, $ctx) 
    {
        $base = $this->base;
        fprintf(STDERR, "Got an error %d (%s) on the listener. "
            ."Shutting down.\n",
            \EventUtil::getLastSocketErrno(),
            \EventUtil::getLastSocketError());
        $base->exit(NULL);
    }
    
    public function connectToCenter ()
    {
        $address = gethostbyname($this->centerHost);
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            echo __METHOD__ . " socket_create() failed: reason: " . socket_strerror(socket_last_error()) . PHP_EOL;
            exit(1);
        }
        $result = socket_connect($socket, $address, $this->centerPort);
        if ($result === false) {
            echo __METHOD__ . "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . PHP_EOL;
            exit(1);
        }
        return $socket;
    }
}