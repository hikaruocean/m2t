<script>
    var channelUserInfo = <?php echo json_encode($channelUserInfo);?>
</script>
<script src="http://code.responsivevoice.org/responsivevoice.js"></script>
<div>
    <div id="player" style="float:left"></div>
    <div id="player-list" style="float:left">
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
    <div style="clear:both"></div>
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
                        if (player == null) {
                            createPlayer(jsonObj.videoId);
                        } else {
                            player.loadVideoById(jsonObj.videoId);
                        }
                        document.title = 'ON AIR';
                        speaker('現在這首歌是' + jsonObj.title);
                        if (jsonObj.comment.length != 0) {
                            setTimeout(function() {
                                speaker(jsonObj.comment);
                            }, commetDelaySecond);
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
    
    function readMsg (smt) {
        var channelString = JSON.stringify(['video_channel_' + channelUserInfo.id, 'global_message']);
        $.ajax({
            url: '//' + window.location.host + ':50126/read',
            type: 'get',
            dataType: 'json',
            data: {channel: channelString, smt: smt},
            success: function (jsonObj) {
                if (jsonObj.result == 0) {
                    for (i in jsonObj.data) {
                        for (key in jsonObj.data[i]) {
                            switch (key) {
                                case 'message':
                                    speaker(jsonObj.data[i][key]);
                                    break;
                                case 'videoList':
                                    $('.v-item').remove();
                                    addItemStr = '';
                                    for (j in jsonObj.data[i][key]) {
                                        jsonObj.data[i][key][j]
                                        addItemStr += '<li class="v-item" qid="' + jsonObj.data[i][key][j].id + '" sort_no="' + jsonObj.data[i][key][j].sort_no + '">' + jsonObj.data[i][key][j].title + '</li>';
                                    }
                                    $('#player-list-ul').append(addItemStr);
                                    break;
                            }
                        }
                    }
                }
                readMsg(jsonObj.smt);
            },
            error: function (jqxhr, textStatus, errorTHrown) {
                setTimeout(readMsg, readMsgRetrySecond);
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
        
        $(document).on('click', '.v-item', function(e) {
            e.preventDefault();
            var _this = $(this);
            var qid = $(this).attr('qid');
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
        readMsg(0);
    });
</script>