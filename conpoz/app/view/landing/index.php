<a href="/landing/getPassport">Try It</a>
<form id="login-form" action="/landing/login" method="POST">
    <table>
        <tr>
            <td>信箱：</td>
            <td><input type="text" name="account" <?php echo $ruleObj->rta('account');?>/><span class="errMsg"></span></td>
        </tr>
        <tr>
            <td>密碼：</td>
            <td><input type="password" name="password"  <?php echo $ruleObj->rta('password');?>/><span class="errMsg"></span></td>
        </tr>
    </table>
    <input type="submit" value="登入" />
</form>
<script>
$('#login-form').ajaxForm({
    dataType: 'json',
    beforeSubmit: function () {
        $('.errMsg').html('');
        var validator = new Validator();
        var errAry = validator.valid($("#login-form"));
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
        location.href = '/';
    }
});
</script>