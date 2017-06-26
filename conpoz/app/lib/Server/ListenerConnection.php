<?php 
namespace Conpoz\App\Lib\Server;

class ListenerConnection 
{
    public $bev, $base, $listener, $channel = array(), $fd, $e, $closeFlag = true, $HEL;

    public function __destruct () 
    {
        echo 'fd ' . $this->fd . ' leave' . PHP_EOL;
        echo 'now connection number is: ' . count($this->listener->conn) . PHP_EOL;
    }

    public function __construct ($base, &$fd, &$e, &$listener) 
    {
        $this->listener = &$listener;
        $this->HEL = $listener->HEL;
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
        if (!isset($reqInfo[1])) {
            $this->responseError('http header error');
            return;
        }
        $pathInfo = explode('?', $reqInfo[1], 2);
        $queryParams = array();
        if (!isset($pathInfo[1])) {
            $this->responseError('req params required');
            return;
        }
        parse_str($pathInfo[1], $queryParams);
        $pathSegment = explode('/', trim($pathInfo[0], '/'));
        switch ($pathSegment[0]) {
            case 'send':
                echo 'send' . PHP_EOL;
                if (!isset($queryParams['data']) || !isset($queryParams['channel'])) {
                    $this->responseError('send action require data, channel');
                    return;
                }
                $smt = microtime(true);
                $sendData = json_decode(urldecode($queryParams['data']), true);
                $sendChannel = json_decode(urldecode($queryParams['channel']), true);
                if (!$sendData || !$sendChannel) {
                    $this->responseError('data, channel need be json format');
                    return;
                }
                // var_dump($sendData, $sendChannel);
                /**
                * 傳送資料連線本身, 立刻回應 request 端成功
                */
                $eb = new \EventBuffer();
                $payload = json_encode(array('result' => 0, 'smt' => $smt));
                $eb->add($this->listener->header200 . base_convert(strlen($payload), 10, 16) . $this->HEL . $payload . $this->HEL . '0' . $this->HEL . $this->HEL);
                $bev->output->addBuffer($eb);
                if (is_string($sendChannel) && $sendChannel == '*') {
                    /**
                    * online channel broadcast case
                    * 寫入各 channel 的 tempBuffer
                    */
                    echo 'brodcast add buffer' . PHP_EOL;
                    foreach ($this->listener->channel as $channelId => &$channelResource) {
                        $channelResource['tempBuffer'][] = $sendData;
                    }
                    unset($channelResource);
                    // var_dump($this->listener->channel);
                } else if (is_array($sendChannel)){
                    /**
                    * 指定 channel 傳送，就寫到該 channel 的 tempBuffer
                    */
                    echo 'specified channel add buffer' . PHP_EOL;
                    $sendChannelLog = '';
                    foreach ($sendChannel as $channelId) {
                        $sendChannelLog .= $channelId . ',';
                        $this->listener->channel[$channelId]['tempBuffer'][] = $sendData;
                    }
                    echo $sendChannelLog . PHP_EOL;
                }
                break;
            case 'read':
                /**
                * 指定 channelId
                */
                if (!isset($queryParams['channel'])) {
                    $this->responseError('send action require channel');
                    return;
                }
                $this->channel = json_decode(urldecode($queryParams['channel']), true);
                if (!$this->channel) {
                    $this->responseError('channel need be json format');
                    return;
                }
                if ($this->listener->keepAlive === true) {
                    $this->closeFlag = false;
                }
                foreach ($this->channel as $channelId) {
                    $this->listener->channel[$channelId]['conn'][$this->fd] = $this;
                }
                break;
            default:
                $eb = new \EventBuffer();
                $eb->add($this->listener->header404 . '0' . $this->HEL . $this->HEL);
                $bev->output->addBuffer($eb);
        }
    }
    
    public function writeCallback ($bev)
    {
        if (0 === $bev->output->length && $this->closeFlag) {
            $this->kill();
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
    
    private function responseError ($err = 'null')
    {
        echo 'input data invalid message: [' . $err . ']' . PHP_EOL;
        $eb = new \EventBuffer();
        $payload = json_encode(array('result' => -1));
        $eb->add($this->listener->header200 . base_convert(strlen($payload), 10, 16) . $this->HEL . $payload . $this->HEL . '0' . $this->HEL . $this->HEL);
        $this->bev->output->addBuffer($eb);
        return;
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
        foreach ($this->channel as $channelId) {
            unset($this->listener->channel[$channelId]['conn'][$this->fd]);
        }
    }
}