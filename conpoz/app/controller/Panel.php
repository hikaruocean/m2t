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
        $channelUserInfo = null;
        $selfChannel = null;
        $this->getChannelUserId($channelUserInfo, $selfChannel);
        $this->view->addView('/htmlTemplate');
        $this->view->addView('/panel/index');
        require($this->view->getView());
    }
    
    public function loadNextVideoAction ($bag)
    {
        $params = $bag->req->getPost(array('id'));
        if ($params['id'] == $bag->sess->user_id) {
            $rh = $bag->dbquery->execute("SELECT id, info_result_code, video_id, title, comment FROM play_queue WHERE user_id = :userId AND status = 0 ORDER BY sort_no ASC LIMIT 1", array('userId' => (int) $params['id']));
            $obj = $rh->fetch();
            if (!$obj) {
                echo json_encode(array('result' => -2));
                return;
            }
            $bag->dbquery->update('play_queue', array('status' => 1), "id = :id AND user_id = :userId", array('id' => (int) $obj->id, 'userId' => (int) $params['id']));
        } else {
            $rh = $bag->dbquery->execute("SELECT id, info_result_code, video_id, title, comment FROM play_queue WHERE user_id = :userId AND status = 1 ORDER BY sort_no DESC LIMIT 1", array('userId' => (int) $params['id']));
            $obj = $rh->fetch();
            if (!$obj) {
                echo json_encode(array('result' => -2));
                return;
            }
        }
        
        if ((int) $obj->info_result_code == -1) {
            list($prot, $ver) = explode('/', $_SERVER["SERVER_PROTOCOL"]);
            $obj->video_id = 'https://www.youtube.com/embed/' . $obj->video_id . '?autoplay=true&enablejsapi=1&origin=' . strtolower($prot) . '%3A%2F%2F' . $_SERVER["HTTP_HOST"] . '&widgetid=1';
        }
        
        echo json_encode(array('result' => (int) $obj->info_result_code, 'id' => $obj->id, 'videoId' => $obj->video_id, 'title' => $obj->title, 'comment' => $obj->comment));
        return;
    }
    
    public function addVideoAction ($bag)
    {
        $params = $bag->req->getPost(array('id', 'url', 'comment'));
        $result = parse_url(trim($params['url']));
        $data = array();
        parse_str($result['query'], $data);
        
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
        return;
    }
    
    public function getVideoListAction ($bag) {
        $params = $bag->req->getPost(array('id', 'firstSortNo', 'lastSortNo'));
        $rh = $bag->dbquery->execute("SELECT id, title, sort_no FROM play_queue WHERE user_id = :userId AND status = 0 AND sort_no < :firstSortNo UNION ALL SELECT id, title, sort_no FROM play_queue WHERE user_id = :userId AND status = 0 AND sort_no > :lastSortNo ORDER BY sort_no ASC", array('userId' => (int) $params['id'], 'firstSortNo' => (float) $params['firstSortNo'], 'lastSortNo' => (float) $params['lastSortNo']));
        $reaultAry = array('prepend' => array(), 'append' => array());
        while ($obj = $rh->fetch()) {
            if ($obj->sort_no < $params['firstSortNo']) {
                $reaultAry['prepend'][] = array(
                    'id' => (int) $obj->id,
                    'title' => $obj->title,
                    'sort_no' => (float) $obj->sort_no,
                );
            } else {
                $reaultAry['append'][] = array(
                    'id' => (int) $obj->id,
                    'title' => $obj->title,
                    'sort_no' => (float) $obj->sort_no,
                );
            }
        }
        echo json_encode($reaultAry);
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
    }
}