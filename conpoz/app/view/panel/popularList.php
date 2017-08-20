<div class="popular-list-form" style="height: 400px; overflow-y: scroll">
    <?php 
    foreach ($rh as $no => $obj):
    ?>
    <div class="popular-video"><?=($no + 1)?>. <?=$obj->title?> <span class="add-video" vid="<?=$obj->id?>">[點播]</span></div>
    <?php 
    endforeach;
    ?>
</div>
<div class="close-news-box">[Close]</div>
<script>
$(function () {
    $(document).on('click', '.close-news-box', function (e) {
        e.preventDefault();
        $('#news-box').slideUp(1000, function () {$('#news-box').html('')});
    });
    $(document).on('click', '.popular-video .add-video', function (e) {
        e.preventDefault();
        var vid = $(this).attr('vid');
        $.ajax({
            url: '/panel/addExistVideo',
            type: 'post',
            data: {id: channelUserInfo.id, vid: vid},
            dataType: 'json',
            success: function (jsonObj) {
                
            },
            error: function () {
                alert('ajax failed');
            }
        });
    })
    $('.register-btn').click(function () {
        $('.register-btn').slideUp(1000);
        $('.register-form').slideDown(1000);
    });
    $('#register-form').ajaxForm({
        dataType: 'json',
        beforeSubmit: function () {
            $('.errMsg').html('');
            var validator = new Validator();
            var errAry = validator.valid($("#register-form"));
            if (errAry.length != 0) {
                $.each(errAry, function (k, v) {
                    v.object.next().html(v.errMsg);
                });
                return false;
            }
            return true;
        },
        success: function (jsonObj) {
            if (jsonObj.result != 0) {
                alert(jsonObj.message);
                return;
            }
            $('#news-box').slideUp(1000, function () {$('#news-box').html('')});
        }
    });
});
</script>
