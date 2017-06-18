<?php 
namespace Conpoz\App\Lib\Server;

class ListenerConnection 
{
    public $bev, $base, $conn, $fd, $e, $sending = false;

    public function __destruct () 
    {
        echo 'fd ' . $this->fd . ' leave' . PHP_EOL;
        echo 'now connection number is: ' . count($this->conn) . PHP_EOL;
    }

    public function __construct ($base, &$fd, &$e, &$conn) 
    {
        $this->conn = &$conn;
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
        parse_str($pathInfo[1], $queryParams);
        $pathSegment = explode('/', trim($pathInfo[0], '/'));
        switch ($pathSegment[0]) {
            case 'send':
                echo 'send' . PHP_EOL;
                $headerStr = 'HTTP/1.1 200 OK' . PHP_EOL .
                            'Server: LPServer/1.0.0' . PHP_EOL .
                            'Content-Type: application/javascript'  . PHP_EOL .
                            'Connection: close' . PHP_EOL . PHP_EOL;
                if (!isset($pathSegment[1])) {
                    $result = $headerStr . $queryParams['callback'] . '(' . json_encode(array('result' => -1)) . ');';
                    $eb = new \EventBuffer();
                    $eb->add($result);
                    $bev->output->addBuffer($eb);
                } else {
                    $smt = microtime(true);
                    $message = urldecode($pathSegment[1]);
                    /**
                    * 寫入 db
                    */
                    $dbquery = \Conpoz\Core\Lib\Util\Container::getService('dbquery');
                    $dbquery->insert('message', array('smt' => $smt, 'message' => $message));
                    foreach ($this->conn as $fd => $listenerConnection) {
                        if ($fd == $this->fd) {
                            $result = $headerStr. 'sendMsgBack(' . json_encode(array('result' => 0)) . ');';
                            $eb = new \EventBuffer();
                            $eb->add($result);
                            if ($listenerConnection->sending == false) {
                                $listenerConnection->sending = true;
                                $listenerConnection->bev->output->addBuffer($eb);
                            }
                            // $result = $headerStr. $queryParams['callback'] . '(' . json_encode(array('result' => 0, 'message' => $message, 'smt' => $smt)). ');';
                            // $eb = new \EventBuffer();
                            // $eb->add($result);
                            // $listenerConnection->bev->output->addBuffer($eb);
                        } else {
                            $result = $headerStr. 'readMsgBack(' . json_encode(array('result' => 0, 'message' => $message, 'smt' => $smt)). ');';
                            $eb = new \EventBuffer();
                            $eb->add($result);
                            if ($listenerConnection->sending == false) {
                                $listenerConnection->sending = true;
                                $listenerConnection->bev->output->addBuffer($eb);
                            }
                        }
                    }
                }
                break;
            case 'read':
                if ($queryParams['smt'] == 0) {
                    return;
                }
                $dbquery = \Conpoz\Core\Lib\Util\Container::getService('dbquery');
                $rh = $dbquery->execute("SELECT message, smt FROM message WHERE smt > :smt ORDER BY smt ASC", array('smt' => (float) $queryParams['smt']));
                if ($rh->rowCount() < 1) {
                    return;
                }
                $headerStr = 'HTTP/1.1 200 OK' . PHP_EOL .
                            'Server: LPServer/1.0.0' . PHP_EOL .
                            'Content-Type: application/javascript'  . PHP_EOL .
                            'Connection: close' . PHP_EOL . PHP_EOL;
                $message = '';
                foreach($rh as $obj) {
                    $message .= $obj->message . '。';
                }
                $result = $headerStr . 'readMsgBack(' . json_encode(array('result' => 0, 'message' => $message, 'smt' => $obj->smt)). ');';
                $eb = new \EventBuffer;
                $eb->add($result);
                if ($this->sending == false) {
                    $this->sending = true;
                    $bev->output->addBuffer($eb);
                }
                break;
            default:
                $headerStr = 'HTTP/1.1 404 NOT FOUND' . PHP_EOL .
                            'Server: LPServer/1.0.0' . PHP_EOL .
                            'Content-Type: text/html; charset=utf-8'  . PHP_EOL .
                            'Connection: close' . PHP_EOL . PHP_EOL;
                $result = $headerStr;
                $eb = new \EventBuffer();
                $eb->add($result);
                if ($this->sending == false) {
                    $this->sending = true;
                    $bev->output->addBuffer($eb);
                }
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
        $this->sending = false;
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
        unset($this->conn[$this->fd]);
    }
}