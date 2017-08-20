<?php 
namespace Conpoz\App\Controller;

use \Conpoz\Core\Lib\Util\Container AS Bag;

class Panel extends \Conpoz\App\Controller\BaseController
{
    
    private function getChannelUserId (&$channelUserInfo = null, &$selfChannel = null)
    {   
        $bag = $this->bag;
        $channel = $bag->req->getQuery(2, $bag->sess->channel);
        if ($channel == $bag->sess->channel) {
            $channelUserInfo = (object) array(
                'id' => $bag->sess->user_id,
                'name' => $bag->sess->name,
                'channel' => $bag->sess->channel,
                'user_role' => $bag->sess->user_role,
            );
            $selfChannel = true;
        } else {
            $selfChannel = false;
            $dbquery = \Conpoz\Core\Lib\Util\Container::getService('dbquery');
            $rh = $dbquery->execute("SELECT id, name, channel, user_role FROM user WHERE channel = :channel", array('channel' => $channel));
            $obj = $rh->fetch();
            if (!$obj) {
                $this->app->dispatch('Error', 'http404');
                exit();
            }
            $channelUserInfo = $obj;
        }
    }
    public function indexAction ($bag)
    {
        /**
        * 未註冊發通知提醒
        **/
        if (empty($bag->sess->account)) {
            $payload = $bag->lpPack->payload(array('user_' . $bag->sess->user_id), array('news' => array('delay' => 2, 'content' => '/member/registerForm')));
            $bag->net->httpGet('http://127.0.0.1:50126/send?' . $payload);
        }
        
        $channelUserInfo = null;
        $selfChannel = null;
        $this->getChannelUserId($channelUserInfo, $selfChannel);
        
        $channelAry = array('video_channel_' . $channelUserInfo->id, 'global_message', 'user_' . $bag->sess->user_id);
        $ts = time();
        $tk = md5(json_encode($channelAry) . $ts . $bag->config->LPServer['hashKey']);
        $lpServerInfo = array(
            'channel' => $channelAry,
            'ts' => $ts,
            'tk' => $tk
        );
        $this->videoListChange($channelUserInfo->id);
        $this->view->addView('/htmlTemplate');
        $this->view->addView('/panel/index');
        require($this->view->getView());
    }
    
    public function loadNextVideoAction ($bag)
    {
        try {
            
            $params = $bag->req->getPost(array('id'));
            
            if ($params['id'] == $bag->sess->user_id) {
                $bag->dbquery->update('play_queue', array('status' => 1), "status = :status AND user_id = :userId", array('status' => 2, 'userId' => (int) $params['id']));
                $rh = $bag->dbquery->execute("SELECT id, info_result_code, video_id, title, comment FROM play_queue WHERE user_id = :userId AND status = 0 ORDER BY sort_no ASC LIMIT 1", array('userId' => (int) $params['id']));
                $obj = $rh->fetch();
                if (!$obj) {
                    throw new \Exception('no video', -2);
                }
                $bag->dbquery->update('play_queue', array('status' => 2), "id = :id AND user_id = :userId", array('id' => (int) $obj->id, 'userId' => (int) $params['id']));
                
            } else {
                $rh = $bag->dbquery->execute("SELECT id, info_result_code, video_id, title, comment FROM play_queue WHERE user_id = :userId AND status = 2", array('userId' => (int) $params['id']));
                $obj = $rh->fetch();
                if (!$obj) {
                    throw new \Exception('channel stop', -3);
                }
            }
            
            $src = null;
            if ((int) $obj->info_result_code == -1) {
                list($prot, $ver) = explode('/', $_SERVER["SERVER_PROTOCOL"]);
                $src = 'https://www.youtube.com/embed/' . $obj->video_id . '?autoplay=true&enablejsapi=1&origin=' . strtolower($prot) . '%3A%2F%2F' . $_SERVER["HTTP_HOST"] . '&widgetid=1';
            }
            
            echo json_encode(array('result' => (int) $obj->info_result_code, 'id' => $obj->id, 'videoId' => $obj->video_id, 'src' => $src, 'title' => $obj->title, 'comment' => $obj->comment));
            
        } catch (\Exception $e) {
            echo json_encode(array('result' => -2));
        }
        $this->videoListChange($params['id']);
        
    }
    
    public function addVideoAction ($bag)
    {
        $params = $bag->req->getPost(array('id', 'url', 'comment'));
        $result = parse_url(trim($params['url']));
        $data = array();
        switch ($result['host']) {
            case 'www.youtube.com':
                parse_str($result['query'], $data);
                break;
            case 'youtu.be':
                $data['v'] = ltrim($result['path'], '/');
                break;
            default :
                echo json_encode(array('result' => -1));
                return;
                break;
        }
        $bag->dbquery->begin();
        $rh = $bag->dbquery->execute("SELECT id, title, info_result_code, order_count FROM video_list WHERE video_id = :videoId FOR UPDATE", array('videoId' => $data['v']));
        if ($rh->rowCount() > 0) {
            $obj = $rh->fetch();
            $bag->dbquery->update('video_list', array('order_count' => $obj->order_count + 1), "id = :id", array('id' => (int) $obj->id));
            
            $vluRh = $bag->dbquery->execute("SELECT id, order_count FROM video_list_user WHERE user_id = :userId AND video_id = :videoId FOR UPDATE", array('userId' => (int) $bag->sess->user_id, 'videoId' => $data['v']));
            if ($vluRh->rowCount() > 0) {
                $vluObj = $vluRh->fetch();
                $bag->dbquery->update('video_list_user', array('order_count' => $vluObj->order_count + 1), "id = :id", array('id' => (int) $vluObj->id));
            } else {
                $bag->dbquery->insert('video_list_user', array('user_id' => (int) $bag->sess->user_id, 'video_id' => $data['v'], 'order_count' => 1));
            }
            $vTitle = $obj->title;
            $infoResultCode = $obj->info_result_code;
        } else {
            $content = file_get_contents('http://youtube.com/get_video_info?video_id=' . $data['v']);
            parse_str($content, $vInfo);
            if ($vInfo['status'] != 'ok') {
                $infoResultCode = -1;
                $returnAry = $bag->net->httpGet($params['url']);
                preg_match('/<title>(.*)\s\-.*<\/title>/',$returnAry['result'], $match);
                $vTitle = $match[1];
            } else {
                $infoResultCode = 0;
                $vTitle = isset($vInfo['title']) ? $vInfo['title'] : '';
            }
            $vTitle = !empty($vTitle) ? $vTitle : '歌曲 Title 找不到 ' . microtime(true);
            $bag->dbquery->insert('video_list', array('url' => $params['url'], 'video_id' => $data['v'], 'title' => $vTitle, 'info_result_code' => (int) $infoResultCode, 'order_count' => 1));
            $bag->dbquery->insert('video_list_user', array('user_id' => (int) $bag->sess->user_id, 'video_id' => $data['v'], 'order_count' => 1));
        }
        
        $bag->dbquery->insert('play_queue', array('user_id' => (int) $params['id'], 'order_user_id' => (int) $bag->sess->user_id, 'url' => $params['url'], 'info_result_code' => (int) $infoResultCode, 'comment' => $params['comment'], 'video_id' => $data['v'], 'title' => $vTitle, 'status' => 0, 'sort_no' => microtime(true)));
        $bag->dbquery->commit();
        echo json_encode(array('result' => 0, 'title' => $vTitle));
        $this->videoListChange($params['id'], array('videoNews' => '[點歌] ' . $vTitle));
    }
    
    private function videoListChange ($userId, $joinMsg = array())
    {
        $bag = $this->bag;
        $dataAry = array_merge(array('videoListChange' => $userId), $joinMsg);
        $payload = $bag->lpPack->payload(array('video_channel_' . $userId), $dataAry);
        $resultAry = $bag->net->httpGet('http://127.0.0.1:50126/send?' . $payload);
    }
    
    public function addExistVideoAction ($bag)
    {
        $params = $bag->req->getPost(array('id', 'vid'));
        $bag->dbquery->begin();
        $rh = $bag->dbquery->execute("SELECT id, url, video_id, title, info_result_code, order_count FROM video_list WHERE id = :id FOR UPDATE", array('id' => $params['vid']));
        $obj = $rh->fetch();
        if (!$obj) {
            $bag->dbquery->rollback();
            echo json_encode(array('result' => -1));
        }
        
        $bag->dbquery->update('video_list', array('order_count' => $obj->order_count + 1), "id = :id", array('id' => (int) $obj->id));
        
        $vluRh = $bag->dbquery->execute("SELECT id, order_count FROM video_list_user WHERE user_id = :userId AND video_id = :videoId FOR UPDATE", array('userId' => (int) $bag->sess->user_id, 'videoId' => $obj->video_id));
        if ($vluRh->rowCount() > 0) {
            $vluObj = $vluRh->fetch();
            $bag->dbquery->update('video_list_user', array('order_count' => $vluObj->order_count + 1), "id = :id", array('id' => (int) $vluObj->id));
        } else {
            $bag->dbquery->insert('video_list_user', array('user_id' => (int) $bag->sess->user_id, 'video_id' => $obj->video_id, 'order_count' => 1));
        }
        $vTitle = $obj->title;
        $infoResultCode = $obj->info_result_code;
        
        $bag->dbquery->insert('play_queue', array('user_id' => (int) $params['id'], 'order_user_id' => (int) $bag->sess->user_id, 'url' => $obj->url, 'info_result_code' => (int) $infoResultCode, 'comment' => '', 'video_id' => $obj->video_id, 'title' => $vTitle, 'status' => 0, 'sort_no' => microtime(true)));
        $bag->dbquery->commit();
        echo json_encode(array('result' => 0, 'title' => $vTitle));
        $this->videoListChange($params['id'], array('videoNews' => '[點歌] ' . $vTitle));
    }
    
    public function getVideoListAction ($bag) 
    {
        $rh = $bag->dbquery->execute("SELECT id, title, sort_no FROM play_queue WHERE user_id = :userId AND status = 0 ORDER BY sort_no ASC", array('userId' => (int) $bag->req->getPost('userId')));
        $resultAry = array();
        while ($obj = $rh->fetch()) {
            $resultAry[] = $obj;
        }
        
        echo json_encode(array('result' => 0, 'data' => $resultAry));
        return;
    }
    
    public function youFirstAction ($bag)
    {
        $params = $bag->req->getPost(array('id', 'qid'));
        $rh = $bag->dbquery->execute("SELECT sort_no FROM play_queue WHERE user_id = :userId AND status = 0 ORDER BY sort_no ASC LIMIT 1", array('userId' => (int) $params['id']));
        $obj = $rh->fetch();
        if (!$obj) {
            echo json_encode(array('result' => -1));
            return;
        }
        $bag->dbquery->update('play_queue', array('sort_no' => $obj->sort_no - 0.0001), "id = :id", array('id' => (int) $params['qid']));
        echo json_encode(array('result' => 0));
        $rh = $bag->dbquery->execute("SELECT title FROM play_queue WHERE id = :id", array('id' => (int) $params['qid']));
        $obj = $rh->fetch();
        $this->videoListChange($params['id'], array('videoNews' => '[插撥]' . $obj->title));
    }
    
    public function youDeleteAction ($bag)
    {
        $params = $bag->req->getPost(array('id', 'qid'));
        $bag->dbquery->update('play_queue', array('status' => 1), "id = :id", array('id' => (int) $params['qid']));
        $this->videoListChange($params['id']);
    }
    
    public function popularListAction ($bag)
    {
        $rh = $bag->dbquery->execute("SELECT a.id, a.title FROM video_list a INNER JOIN (SELECT id FROM video_list ORDER BY order_count DESC LIMIT 100 OFFSET 0) b ON a.id = b.id");
        $this->view->addView('/panel/popularList');
        require($this->view->getView());
    }
}