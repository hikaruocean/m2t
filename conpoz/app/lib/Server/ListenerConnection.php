<?php 
namespace Conpoz\App\Lib\Server;

class ListenerConnection 
{
    public $bev, $base, $listener, $userId = -1, $fd, $e;

    public function __destruct () 
    {
        echo 'fd ' . $this->fd . ' leave' . PHP_EOL;
        echo 'now connection number is: ' . count($this->listener->conn) . PHP_EOL;
    }

    public function __construct ($base, &$fd, &$e, &$listener) 
    {
        $this->listener = &$listener;
        $this->fd = &$fd;
        $this->e = &$e;
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
        // Copy all the data from the input buffer to the output buffer
        // Variant #1
        $no = 0;
        $header = array();
        $echo = '';
        while (!is_null($line = $bev->input->readLine(\EventBuffer::EOL_CRLF))) {
            if ($no === 0) {
                $reqInfo = explode(' ', $line);
            } else {
                $tempData = explode(': ', $line, 2);
                if (count($tempData) == 2) {
                    $header[$tempData[0]] = $tempData[1];
                } else {
                    $header[$tempData[0]] = null;
                }
            }
            $echo .= $line;
            $no++;
        }
        $pathInfo = explode('?', $reqInfo[1], 2);
        $queryParams = array();
        if (isset($pathInfo[1])) {
            parse_str($pathInfo[1], $queryParams);
        }
        $pathSegment = explode('/', trim($pathInfo[0], '/'));
        switch ($pathSegment[0]) {
            case 'send':
                echo 'send' . PHP_EOL;
                if (!isset($queryParams['data']) || !isset($queryParams['target'])) {
                    $eb = new \EventBuffer();
                    $eb->add($this->listener->header200 . json_encode(array('result' => -1)));
                    $bev->output->addBuffer($eb);
                    return;
                }
                $smt = microtime(true);
                $sendData = json_decode(urldecode($queryParams['data']), true);
                $sendTarget = json_decode(urldecode($queryParams['target']), true);
                // var_dump($sendData, $sendTarget);
                /**
                * 傳送資料連線本身, 立刻回應 request 端成功
                */
                $eb = new \EventBuffer();
                $eb->add($this->listener->header200 . json_encode(array('result' => 0, 'smt' => $smt)));
                $bev->output->addBuffer($eb);
                if (is_string($sendTarget) && $sendTarget = '*') {
                    /**
                    * online user broadcast case
                    * 寫入各 user 的 tempBuffer
                    */
                    echo 'brodcast add buffer' . PHP_EOL;
                    foreach ($this->listener->user as $userId => &$userResource) {
                        $userResource['tempBuffer'][] = $sendData;
                    }
                    unset($userResource);
                    // var_dump($this->listener->user);
                } else if (is_array($sendTarget)){
                    /**
                    * 指定 user 傳送，就寫到該 user 的 tempBuffer
                    */
                    echo 'specified user add buffer' . PHP_EOL;
                    foreach ($sendTarget as $userId) {
                        $this->listener->user[$userId]['tempBuffer'][] = $sendData;
                    }
                }
                break;
            case 'read':
                /**
                * 指定 userId
                */
                if (!isset($queryParams['userId'])) {
                    $eb = new \EventBuffer();
                    $eb->add($this->listener->header200 . json_encode(array('result' => -1)));
                    $bev->output->addBuffer($eb);
                    return;
                }
                $this->userId = $queryParams['userId'];
                $this->listener->user[$this->userId]['conn'][$this->fd] = $this;
                break;
            default:
                $eb = new \EventBuffer();
                $eb->add($this->listener->header404);
                $bev->output->addBuffer($eb);
        }
    }
    
    public function writeCallback ($bev)
    {
        if (0 === $bev->output->length) {
            $this->kill();
        }
    }

    public function eventCallback ($bev, $events, $ctx) 
    {
        if ($events & \EventBufferEvent::ERROR) {
            echo "Error from bufferevent\n";
            $this->kill();
        }

        if ($events & (\EventBufferEvent::EOF | \EventBufferEvent::ERROR)) {
            echo "EOF | ERROR" . PHP_EOL;
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
        $this->e->delTimer();
        $this->e = null;
        unset($this->listener->conn[$this->fd]);
        if ($this->userId != -1) {
            unset($this->listener->user[$this->userId]['conn'][$this->fd]);
        }
    }
}