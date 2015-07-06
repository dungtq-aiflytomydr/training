<?php echo $this->Html->script('Users/processUser'); ?>
<div class="popupLogin">
    <form id="frmRegister" action="" method="post" autocomplete="off">
        <div class="form-group">
            <label for="exampleInputEmail">Email</label>
            <input type="email" id="u-email" name="email" class="form-control" placeholder="Email">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword">Password</label>
            <input type="password" id="u-pw" name="password" class="form-control" placeholder="Password">
        </div>
        <div class="form-group">
            <label for="exampleInputName">Name</label>
            <input type="text" id="u-name" name="name" class="form-control" placeholder="Name">
        </div>
        <button type="button" id="btn-register" class="btn btn-default">Register</button>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Users.init();
    });
</script>
