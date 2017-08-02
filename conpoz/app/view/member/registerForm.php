<span class="register-btn">您也可以選擇現在註冊帳號</span>
<div class="register-form" style="display:none">
    <form id="register-form" action="/member/register" method="post">
        <table>
            <tr>
                <td>信箱：</td>
                <td><input type="text" id="account" name="account" <?php echo $ruleObj->rta('account'); ?>/><span class="errMsg"></span></td>
            </tr>
            <tr>
                <td>密碼：</td>
                <td><input type="password" id="password" name="password" <?php echo $ruleObj->rta('password'); ?>/><span class="errMsg"></span></td>
            </tr>
            <tr>
                <td>再次輸入密碼：</td>
                <td><input type="password" id="retype_password" name="retype_password" <?php echo $ruleObj->rta('retype_password'); ?>/><span class="errMsg"></span></td>
            </tr>
            <tr>
                <td>匿稱：</td>
                <td><input type="text" id="name" name="name" <?php echo $ruleObj->rta('name'); ?>/><span class="errMsg"></span></td>
            </tr>
            <tr>
                <td>頻道：</td>
                <td><?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/panel/index/';?><input type="text" id="channel" name="channel" value="<?php echo $bag->sess->channel;?>" <?php echo $ruleObj->rta('channel'); ?>/><span class="errMsg"></span></td>
            </tr>
        </table>
        <input type="submit" id="register-form-btn" value="註冊"/>
    </form>
</div>
<script>
$(function () {
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
