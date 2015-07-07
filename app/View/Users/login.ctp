<div class="popupLogin">
    <?php echo $this->Session->flash('auth'); ?>

    <form id="frmRegister" action="/users/login" method="post">
        <div class="form-group">
            <label for="exampleInputEmail">Email</label>
            <input type="email" id="u-email" name="data[User][email]" class="form-control" placeholder="Email">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword">Password</label>
            <input type="password" id="u-pw" name="data[User][password]" class="form-control" placeholder="Password">
        </div>
        <button type="submit" id="btn-login" class="btn btn-default">Login</button>
        <a href="/users/forgot_pw">Forgot password?</a>
    </form>
</div>