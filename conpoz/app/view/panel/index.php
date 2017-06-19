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
    var commetDelaySecond = 30000;
    var noListReloadSecond = 10000;
    var firstSortNo = 0;
    var lastSortNo = 0;
    var getListSwitch = true;
    
    function createPlayer(videoId) {
        player = new YT.Player('player', {
            height: '390',
            width: '640',
            videoId: videoId,
            events: {
                'onReady': onPlayerReady
            }
        });
    }

    var sid = setInterval(function() {
        if (player != null) {
            var state = player.getPlayerState();
            if (0 == state && state != null && loadNextVideoFlag == false) {
                //   $('#log').append('<span>' + state + '</span>');
                loadNextVideoFlag = true;
                onPlayerStateChange({
                    data: state
                });
            }
            if (1 == state && loadNextVideoFlag == true) {
                loadNextVideoFlag = false;
            }
        }
    }, 100);
    
    function onPlayerReady(event) {
        event.target.playVideo();
    }

    function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.ENDED) {
            loadNextVideo();
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
                        setTimeout(loadNextVideo, noListReloadSecond);
                        break;
                    case -1:
                        if (!player) {
                            createPlayer(null);
                        }
                        $('.v-item[qid=' + jsonObj.id + ']').remove();
                        if ($('.v-item').length > 0) {
                            firstSortNo = $('.v-item').eq(0).attr('sort_no');
                        }
                        $('#player').attr('src', jsonObj.videoId);
                        speaker('現在這首歌是' + jsonObj.title);
                        if (jsonObj.comment.length != 0) {
                            setTimeout(function() {
                                speaker(jsonObj.comment);
                            }, commetDelaySecond);
                        }
                        break;
                    case 0:
                        if (!player) {
                            createPlayer(jsonObj.videoId);
                        } else {
                            player.loadVideoById(jsonObj.videoId);
                        }
                        $('.v-item[qid=' + jsonObj.id + ']').remove();
                        if ($('.v-item').length > 0) {
                            firstSortNo = $('.v-item').eq(0).attr('sort_no');
                        }
                        speaker('現在這首歌是' + jsonObj.title);
                        if (jsonObj.comment.length != 0) {
                            setTimeout(function() {
                                speaker(jsonObj.comment);
                            }, commetDelaySecond);
                        }
                        break;
                }
                getList();
            },
            error: function() {
                alert('error');
            }
        });
    }

    function speaker(text) {
        //US English Female
        responsiveVoice.speak(text, 'Chinese Female', {
            onend: function(EndCallback) {}
        });
    }

    function getList() {
        if (getListSwitch) {
            getListSwitch = false;
            $.ajax({
                url: '/panel/getVideoList',
                type: 'post',
                dataType: 'json',
                data: {id: channelUserInfo.id, firstSortNo: firstSortNo, lastSortNo: lastSortNo},
                success: function(jsonObj) {
                    if (firstSortNo == 0 && jsonObj.append.length > 0) {
                        firstSortNo = jsonObj.append[0].sort_no;
                    }
                    if (lastSortNo == 0 && jsonObj.append.length > 0) {
                        lastSortNo = jsonObj.append[jsonObj.append.length - 1].sort_no;
                    }
                    for (i in jsonObj.prepend) {
                        if (jsonObj.prepend[i].sort_no < firstSortNo) {
                            firstSortNo = jsonObj.prepend[i].sort_no;
                        }
                        $('#player-list-ul').prepend('<li class="v-item" qid="' + jsonObj.prepend[i].id + '" sort_no="' + jsonObj.prepend[i].sort_no + '">' + jsonObj.prepend[i].title + '</li>');
                    }
                    for (i in jsonObj.append) {
                        if (jsonObj.append[i].sort_no > lastSortNo) {
                            lastSortNo = jsonObj.append[i].sort_no;
                        }
                        $('#player-list-ul').append('<li class="v-item" qid="' + jsonObj.append[i].id + '" sort_no="' + jsonObj.append[i].sort_no + '">' + jsonObj.append[i].title + '</li>');
                    }
                    getListSwitch = true;
                },
                error: function() {
                    getListSwitch = true;
                    alert('error');
                }
            });
        }
    }
    
    function readMsg (smt) {
        $.ajax({
            url: '//music2gether.lo:50126/read',
            type: 'get',
            dataType: 'json',
            data: {userId: channelUserInfo.id, smt: smt},
            success: function (jsonObj) {
                if (jsonObj.result == 0) {
                    for (i in jsonObj.data) {
                        for (key in jsonObj.data[i]) {
                            switch (key) {
                                case 'message':
                                    speaker(jsonObj.data[i][key]);
                                    break;
                            }
                        }
                    }
                }
                readMsg(jsonObj.smt);
            },
            error: function (jqxhr, textStatus, errorTHrown) {
            }
        });
        // $.ajax({
        //     url: '//music2gether.lo:50126/read',
        //     type: 'get',
        //     dataType: 'jsonp',
        //     data: {smt: smt},
        //     jsonp: 'callback',
        //     jsonpCallback: 'readMsgBack',
        //     success: function (jsonObj) {
        //         if (jsonObj.result == 0) {
        //             speaker(jsonObj.message);
        //         }
        //         readMsg(jsonObj.smt);
        //     },
        //     error: function (jqxhr, textStatus, errorTHrown) {
        //     }
        // });
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
                url: '//music2gether.lo/message/send',
                type: 'post',
                dataType: 'json',
                data: {message: $('#message').val()},
                success: function (jsonObj) {
                    if (jsonObj.result != 0) {
                        return;
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
                        alert(jsonObj.reason);
                        return;
                    }
                    getList();
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
                    _this.remove();
                    getList();
                },
                error: function() {

                }
            });
        });
        
        setInterval(getList, 8000);
        getList();
        readMsg(0);
    });
</script>