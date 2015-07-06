<?php echo $this->Html->script('Users/processUser'); ?>
<div class="popupLogin">
    <form id="frmSetting" action="" method="post" autocomplete="off">
        <div class="form-group">
            <label>Old password</label>
            <input type="password" id="u-oldpw" name="old_pw" class="form-control">
        </div>
        <div class="form-group">
            <label for="">New password</label>
            <input type="password" id="u-pw" name="new_pw" class="form-control">
        </div>
        <div class="form-group">
            <label for="">Confirm password</label>
            <input type="password" id="u-name" name="confirm_pw" class="form-control">
        </div>
        <button type="button" id="btn-changepw" class="btn btn-default">Change password</button>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Users.init();
    });
</script>
