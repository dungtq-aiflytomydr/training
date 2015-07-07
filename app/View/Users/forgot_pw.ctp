<?php echo $this->Html->script('Users/processUser'); ?>
<div class="popupLogin">
    <?php echo $this->Session->flash('auth'); ?>

    <form id="frmForgotPw" action="/users/forgot_pw" method="post">
        <div class="form-group">
            <label for="exampleInputEmail">Email</label>
            <input type="email" id="u-email" name="data[User][email]" class="form-control" placeholder="Email">
        </div>
        <button type="button" id="btn-forgotPw" class="btn btn-default">Send me password</button>
    </form>
</div>
<script>
    jQuery(document).ready(function () {
        Users.init();
    });
</script>