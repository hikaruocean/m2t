<?php 
namespace Conpoz\App\Lib\Server;

class ListenerConnection 
{
    public $bev, $base, $fd, $e = null, $userId, $headerStr, $listener, $sTimestamp;

    public function __destruct () 
    {
        echo 'fd ' . $this->fd . ' leave' . PHP_EOL;
    }

    public function __construct ($base, &$fd, &$listener) 
    {
        $this->listener = &$listener;
        $this->fd = &$fd;
        $this->base = $base;
        $this->headerStr = 'HTTP/1.1 200 OK' . PHP_EOL .
                    'Server: LPServer/1.0.0' . PHP_EOL .
                    'Content-Type: application/json'  . PHP_EOL .
                    'Connection: close' . PHP_EOL .
                    'Access-Control-Allow-Origin: http://music2gether.lo' . PHP_EOL . PHP_EOL;

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
                if (!isset($pathSegment[1])) {
                    $eb = new \EventBuffer();
                    $eb->add($this->headerStr . json_encode(array('result' => -1)));
                    $bev->output->addBuffer($eb);
                } else {
                    $message = urldecode($pathSegment[1]);
                    /**
                    * php curl /send 程序，須馬上回傳結果
                    */
                    $eb = new \EventBuffer();
                    $eb->add($this->headerStr . json_encode(array('result' => 0)));
                    $bev->output->addBuffer($eb);
                    /**
                    * broadcast case
                    */
                    foreach ($this->listener->member as $id => &$v) {
                        if ($v['fd'] == $this->fd) {
                            /**
                            * php curl /send 程序，須馬上回傳結果
                            */
                            // $eb = new \EventBuffer();
                            // $eb->add($this->headerStr . json_encode(array('result' => 0)));
                            // $v['conn']->bev->output->addBuffer($eb);
                        } else {
                            /**
                            * 所有會員通知，存入 tmepBuffer
                            */
                            $v['tempBuffer'][] = array('message' => $message);
                            // $eb = new \EventBuffer();
                            // $eb->add($this->headerStr . json_encode(array('message' => $message)));
                            // $v->conn->bev->output->addBuffer($eb);
                        }
                    }
                }
                break;
            case 'read':
                $this->sTimestamp = time() + 5;
                $this->userId = $queryParams['userId'];
                $this->listener->member[$this->userId]['fd'] = $this->fd;
                $this->listener->member[$this->userId]['conn'] = $this;
                /**
                * add read timer
                */
                $e = \Event::Timer($this->base, function ($data) use (&$e) {
                    if (!empty($this->listener->member[$this->userId]['tempBuffer'])) {
                        echo 'add to buffer' . PHP_EOL;
                        $eb = new \EventBuffer();
                        $resultAry = array();
                        while($tempData = array_shift($this->listener->member[$this->userId]['tempBuffer'])) {
                            $resultAry[] = $tempData;
                        }
                        $eb->add($this->listener->member[$this->userId]['conn']->headerStr . json_encode(array('result' => 0, 'data' => $resultAry, 'smt' => microtime(true))));
                        $this->listener->member[$this->userId]['conn']->bev->output->addBuffer($eb);
                        return;
                    }
                    if (time() > $this->sTimestamp) {
                        echo 'expired' . PHP_EOL;
                        $eb = new \EventBuffer();
                        $eb->add($this->listener->member[$this->userId]['conn']->headerStr . json_encode(array('result' => -1, 'smt' => microtime(true))));
                        $this->listener->member[$this->userId]['conn']->bev->output->addBuffer($eb);
                        $e->delTimer();
                    } else {
                        $e->addTimer(1);
                    }
                });
                $e->data = $e;
                $e->addTimer(1);
                $this->e = &$e;
                break;
            default:
                $headerStr = 'HTTP/1.1 404 NOT FOUND' . PHP_EOL .
                            'Server: LPServer/1.0.0' . PHP_EOL .
                            'Content-Type: text/html; charset=utf-8'  . PHP_EOL .
                            'Connection: close' . PHP_EOL . PHP_EOL;
                $result = $headerStr;
                $eb = new \EventBuffer();
                $eb->add($result);
                $bev->output->addBuffer($eb);
        }
        
        /* Variant #2 */
        /*
        $input    = $bev->getInput();
        $output = $bev->getOutput();
        $output->addBuffer($input);
        */
    }
    
    public function writeCallback ($bev)
    {
        if (0 === $bev->output->length) {
            echo 'call kill' . PHP_EOL;
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
        if (!is_null($this->e)) {
            $this->e->delTimer();
            $this->e = null;
        }
        var_dump(array_keys($this->listener->member));
        $this->listener->member[$this->userId]['fd'] = null;
        $this->listener->member[$this->userId]['conn'] = null;
    }
}