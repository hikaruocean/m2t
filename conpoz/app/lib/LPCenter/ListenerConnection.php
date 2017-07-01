<?php 
namespace Conpoz\App\Lib\LPCenter;

class ListenerConnection 
{
    public $bev, $base, $listener, $fd, $HEL, $channel;

    public function __destruct () 
    {
        echo 'fd ' . $this->fd . ' leave' . PHP_EOL;
        echo 'now connection number is: ' . count($this->listener->conn) . PHP_EOL;
    }

    public function __construct ($base, &$fd, &$listener) 
    {
        $this->channel = array();
        $this->listener = &$listener;
        $this->HEL = $listener->HEL;
        $this->fd = &$fd;
        $this->base = $base;
        $this->bev = new \EventBufferEvent($base, $fd, \EventBufferEvent::OPT_CLOSE_ON_FREE);

        $this->bev->setCallbacks(array($this, "readCallback"), array($this, "writeCallback"),
            array($this, "eventCallback"), null);

        if (!$this->bev->enable(\Event::READ)) {
            echo "Failed to enable READ\n";
            return;
        }
    }

    public function readCallback ($bev, $ctx) 
    {
        while (!is_null($line = $bev->input->readLine(\EventBuffer::EOL_CRLF))) {
            echo 'recv data : ' . $line . $this->HEL;
            $obj = json_decode($line);
            switch ($obj->action) {
                case 'register':
                    foreach ($obj->channel as $channelId) {
                        if (!in_array($channelId, $this->channel)) {
                            $this->channel[] = $channelId;
                        }
                        $this->listener->channel[$channelId]['conn'][$this->fd] = $this;
                    }
                    break;
                case 'unregister':
                    foreach ($obj->channel as $channelId) {
                        unset($this->channel[$channelId]);
                        unset($this->listener->channel[$channelId]['conn'][$this->fd]);
                    }
                    break;
                case 'broadcast':
                    $payloadSeg1 = 'GET /centerMessage?data=' .  urlencode(json_encode($obj->data));
                    $payloadSeg2 = ' HTTP/1.1' . $this->HEL . $this->HEL;
                    $readySendAry = array();
                    foreach ($obj->channel as $channelId) {
                        if (!isset($this->listener->channel[$channelId])) {
                            continue;
                        }
                        foreach ($this->listener->channel[$channelId]['conn'] as $fd => $conn) {
                            $readySendAry[$fd][] = $channelId;
                        }
                    }
                    foreach ($readySendAry as $fd => $channel) {
                        if ($fd == $this->fd) {
                            continue;
                        }
                        $payload = $payloadSeg1 . '&channel=' . urlencode(json_encode($channel)) . $payloadSeg2;
                        $eb = new \EventBuffer();
                        $eb->add($payload);
                        $this->listener->conn[$fd]->bev->output->addBuffer($eb);
                    }
                    // foreach ($this->listener->conn as $fd => $conn) {
                    //     if ($fd == $this->fd) {
                    //         continue;
                    //     }
                    //     $eb = new \EventBuffer();
                    //     $eb->add($paylaod);
                    //     $conn->bev->output->addBuffer($eb);
                    // }
                    break;
                case 'flush':
                    break;
            }
        }
    }
    
    public function writeCallback ($bev)
    {
        if (0 === $bev->output->length) {
            //do nothing
        }
    }

    public function eventCallback ($bev, $events, $ctx) 
    {
        if ($events & \EventBufferEvent::ERROR) {
            echo "ERROR" . PHP_EOL;
            echo \EventUtil::getLastSocketError() . PHP_EOL;
            $this->kill();
        }

        if ($events & (\EventBufferEvent::EOF)) {
            echo "EOF" . PHP_EOL;
            $this->kill();
        }
        
        if ($events & \EventBufferEvent::TIMEOUT) {
            echo 'timeout' . PHP_EOL;
            $this->kill();
        }
    }
        
    public function kill ()
    {
        $this->bev->free();
        /**
        * $this->bev = null 是關鍵，若沒這樣做，判定有循環指向，不會呼叫 __destruct
        */
        $this->bev = null;
        unset($this->listener->conn[$this->fd]);
        if (is_array($this->channel)) {
            foreach ($this->channel as $channelId) {
                unset($this->listener->channel[$channelId]['conn'][$this->fd]);
            }
        }
    }
}