<!--<div class="popupLogin">
    <form id="frmChangePw" action="/users/updateUser/password" method="post" autocomplete="off">
        <div class="form-group">
            <label>Old password</label>
            <input type="password" id="u-oldpw" name="old_pw" class="form-control">
            <small class="msg-error"></small>
        </div>
        <div class="form-group">
            <label for="">New password</label>
            <input type="password" id="u-newpw" name="new_pw" class="form-control">
            <small class="msg-error"></small>
        </div>
        <div class="form-group">
            <label for="">Confirm password</label>
            <input type="password" id="u-confirmpw" name="confirm_pw" class="form-control">
            <small class="msg-error"></small>
        </div>
        <button type="button" id="btn-changepw" disabled class="btn btn-default">Change password</button>
    </form>
</div>
<hr/>-->

<div class="popupLogin">
    <?php
    echo $this->Form->create('User', array(
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group'
            )
        )
    ));
    echo $this->Form->input('old_pw', array(
        'label' => 'Old Password',
        'type' => 'password',
        'class' => 'form-control'
    ));
    echo $this->Form->input('new_pw', array(
        'label' => 'New Password',
        'type' => 'password',
        'class' => 'form-control'
    ));
    echo $this->Form->input('confirm_pw', array(
        'label' => 'Confirm Password',
        'type' => 'password',
        'class' => 'form-control'
    ));
    echo $this->Form->end(array(
        'label' => 'Change password',
        'div' => array(
            'class' => 'form-group'
        ),
        'class' => 'btn btn-default'
    ));
    ?>
</div>