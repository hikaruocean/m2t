<?php
namespace Conpoz\App\Lib\Server;

class Listener 
{
    public $base,
        $listener,
        $socket;
    public $member = array();

    public function __destruct () 
    {
        foreach ($this->member as $id => &$v) {
            $v->tempBuffer = null;
            $v->conn = null;
            $v->fd = null;
            $v = null;
        }
    }

    public function __construct ($port) 
    {
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
             "0.0.0.0:$port");

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
        
        $base = $this->base;
        new \Conpoz\App\Lib\Server\ListenerConnection($base, $fd, $this);
        
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
}