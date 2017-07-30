<script>
    var channelUserInfo = <?php echo json_encode($channelUserInfo);?>;
    var lpServerInfo = <?php echo json_encode($lpServerInfo);?>;
</script>
<script src="http://code.responsivevoice.org/responsivevoice.js"></script>
<style>
.main-div {
    color: #ffffff;
    font-size: 24px;
    font-weight: bold;
    padding: 10px;
    background-color: #5cb85c;
    overflow: auto;
}
.right {
    float: right;
}
#news-box {
    display: none;
    color: #ffffff;
    padding: 10px;
    background-color: #5bc0de;
}
#player-frame {
    position: relative;
    /*border: 1px solid red;*/
}
#video-news {
    position: absolute;
    color: #FFE66F;
    top: 0px;
    left: 0px;
    padding: 20px;
    text-shadow: 1px 1px #333333;
    width: 440px;
    word-wrap: break-word;
}
</style>
<div>
    <div class="main-div">
        <span>
            <?php echo $channelUserInfo->name;?>
        </span>
        <span>
            <?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/panel/index/' . $channelUserInfo->channel;?>
        </span>
        <span class="right">
            <a href="/landing/logout">Log Out</a>
        </span>
    </div>
    <div id="news-box"></div>
    <div id="player-frame">
        <div id="player"></div>
        <div id="video-news"></div>
    </div>
    <div id="player-list">
        <div>
            <textarea id="message" name="message" placeholder="發送訊息"></textarea>
        </div>
        <input id="send" type="button" value="send">
        <div>
            <textarea id="url" name="url" placeholder="youtube 網址"></textarea>
        </div>
        <div>
            <textarea id="comment" name="comment" placeholder="點歌留言"></textarea>
        </div>
        <input id="add" type="button" value="add">
        <ul id="player-list-ul">
        </ul>
    </div>
    <div>
    </div>
</div>
<div id="log">
</div>
<!-- <iframe id="loadFrame" style="width:100;height:100;border:0; border:none;">
</iframe> -->
<script>
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    var player = null;
    var loadNextVideoFlag = false;
    var commetDelaySecond = 100000;
    var noListReloadSecond = 5000;
    var readMsgRetrySecond = 10000;
    var firstSortNo = 0;
    var lastSortNo = 0;
    var getListSwitch = true;
    var lastPlayQueueId = 0;
    var tempContent = '';
    
    function createPlayer(videoId) {
        player = new YT.Player('player', {
            height: '270',
            width: '480',
            videoId: videoId,
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    }

    /**
    * 測試版在網址為 ip 的情況下
    * 自己設定 onStateChnage 來對版權問題 workaround
    */
    // var sid = setInterval(function() {
    //     if (player != null && typeof player.getPlayerState !== 'undefined') {
    //         var state = player.getPlayerState();
    //         if (0 == state && state != null && loadNextVideoFlag == false) {
    //             //   $('#log').append('<span>' + state + '</span>');
    //             loadNextVideoFlag = true;
    //             onPlayerStateChange({
    //                 data: state
    //             });
    //         }
    //         if (1 == state && loadNextVideoFlag == true) {
    //             loadNextVideoFlag = false;
    //         }
    //     }
    // }, 100);
    
    function onPlayerReady(event) {
        event.target.playVideo();
    }

    function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.ENDED) {
            loadNextVideoFlag = true;
            loadNextVideo();
        }
        if (event.data == YT.PlayerState.PLAYING) {
            loadNextVideoFlag = false;
        }
    }

    function onYouTubeIframeAPIReady() {
        loadNextVideo();
    }
    
    function stopVideo() {
        player.stopVideo();
    }
    
    function loadNextVideo() {
        $.ajax({
            url: '/panel/loadNextVideo',
            type: 'post',
            dataType: 'json',
            data: {
                id: channelUserInfo.id
            },
            success: function(jsonObj) {
                switch (jsonObj.result) {
                    case -2:
                        if (player == null) {
                            createPlayer(null);
                        }
                        document.title = 'OFF AIR';
                        setTimeout(loadNextVideo, noListReloadSecond);
                        break;
                    case -1:
                        /**
                        * 測試版在網址為 ip 的情況下
                        * 用 src replace 來對版權問題 workaround
                        */
                        // if (player == null) {
                        //     createPlayer(null);
                        // }
                        // document.title = 'ON AIR';
                        // $('#player').attr('src', jsonObj.src);
                        // speaker('現在這首歌是' + jsonObj.title);
                        // if (jsonObj.comment.length != 0) {
                        //     setTimeout(function() {
                        //         speaker(jsonObj.comment);
                        //     }, commetDelaySecond);
                        // }
                        // break;
                    case 0:
                        if (lastPlayQueueId == jsonObj.id) {
                            setTimeout(loadNextVideo, noListReloadSecond);
                        } else {
                            if (player == null) {
                                createPlayer(jsonObj.videoId);
                            } else {
                                player.loadVideoById(jsonObj.videoId);
                            }
                            document.title = 'ON AIR';
                            lastPlayQueueId = jsonObj.id;
                            speaker('現在這首歌是' + jsonObj.title);
                            if (jsonObj.comment.length != 0) {
                                setTimeout(function() {
                                    speaker(jsonObj.comment);
                                }, commetDelaySecond);
                            }
                        }
                        break;
                }
            },
            error: function() {
                alert('error');
            }
        });
    }

    function speaker(text) {
        //US English Female
        responsiveVoice.speak(text, 'Chinese Female', {
            onstart: function () {
                if (player != null && typeof player.setVolume !== 'undefined') {
                    player.setVolume(30);
                }
            },
            onend: function() {
                if (player != null && typeof player.setVolume !== 'undefined') {
                    player.setVolume(100);
                }
            }
        });
    }
    
    function readMsg (smt, ts, tk) {
        var channelString = JSON.stringify(lpServerInfo.channel);
        $.ajax({
            url: '//' + window.location.host + ':50126/read',
            type: 'get',
            dataType: 'json',
            data: {channel: channelString, ts: ts, tk: tk, smt: smt},
            success: function (jsonObj) {
                switch (jsonObj.result) {
                    case 0:
                        for (i in jsonObj.data) {
                            for (key in jsonObj.data[i]) {
                                switch (key) {
                                    case 'message':
                                        speaker(jsonObj.data[i][key]);
                                        break;
                                    case 'videoListChange':
                                        $.ajax({
                                            url: '/panel/getVideoList',
                                            type: 'post',
                                            dataType: 'json',
                                            data: {userId: jsonObj.data[i][key]},
                                            success: function (jsonObj) {
                                                $('.v-item').remove();
                                                addItemStr = '';
                                                for (p in jsonObj.data) {
                                                    addItemStr += '<li class="v-item" qid="' + jsonObj.data[p].id + '" sort_no="' + jsonObj.data[p].sort_no + '">' + jsonObj.data[p].title + '[<a class="v-first">插撥</a>][<a class="v-delete">刪除</a>]</li>';
                                                }
                                                $('#player-list-ul').append(addItemStr);
                                            }
                                        });
                                        break;
                                    case 'news':
                                        tempContent = jsonObj.data[i][key].content;
                                        setTimeout(function () {
                                            $('#news-box').html(tempContent).slideDown(1000);
                                        }, jsonObj.data[i][key].delay * 1000);
                                        break;
                                    case 'videoNews':
                                        $('#video-news').html(jsonObj.data[i][key]);
                                        break;
                                }
                            }
                        }
                        readMsg(jsonObj.smt, jsonObj.ts, jsonObj.tk);
                        break;
                    case -1:
                        readMsg(jsonObj.smt, jsonObj.ts, jsonObj.tk);
                        break;
                    case -2:
                        break;
                }
            },
            error: function (jqxhr, textStatus, errorTHrown) {
                setTimeout(function(){readMsg();}, readMsgRetrySecond);
                jqxhr.abort();
            }
        });
    }
    
</script>
<script>
    $(function() {
        // $('#loadFrame').load(function () {
        // });
        // $('#url').blur(function () {
        //     $('#loadFrame').attr('src', $(this).val());
        // });
        $('#send').click(function () {
            $.ajax({
                url: '/message/send',
                type: 'post',
                dataType: 'json',
                data: {message: $('#message').val()},
                success: function (jsonObj) {
                    if (jsonObj.result != 0) {
                    }
                    $('#message').val('');
                },
                error: function (jqxhr, textStatus, errorTHrown) {
                }
            });
        });
        
        $('#add').click(function() {
            $.ajax({
                url: '/panel/addVideo',
                type: 'post',
                dataType: 'json',
                data: {
                    id: channelUserInfo.id,
                    url: $('#url').val(),
                    comment: $('#comment').val()
                },
                success: function(jsonObj) {
                    if (jsonObj.result != 0) {
                        alert('URL is not from YOUTUBE');
                        return;
                    }
                }
            });
            $('#url').val('');
            $('#comment').val('');
        });
        
        $(document).on('click', '.v-first', function(e) {
            e.preventDefault();
            var _this = $(this);
            var qid = _this.parent().attr('qid');
            $.ajax({
                url: '/panel/youFirst',
                type: 'post',
                dataType: 'json',
                data: {
                    id: channelUserInfo.id,
                    qid: qid
                },
                success: function(jsonObj) {
                    
                },
                error: function() {

                }
            });
        });
        
        $(document).on('click', '.v-delete', function(e) {
            e.preventDefault();
            var _this = $(this);
            var qid = _this.parent().attr('qid');
            $.ajax({
                url: '/panel/youDelete',
                type: 'post',
                dataType: 'json',
                data: {
                    id: channelUserInfo.id,
                    qid: qid
                },
                success: function(jsonObj) {
                    
                },
                error: function() {

                }
            });
        });
        readMsg(0, lpServerInfo.ts, lpServerInfo.tk);
    });
</script>