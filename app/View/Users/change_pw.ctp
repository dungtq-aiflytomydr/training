<div class="popupLogin">
    <?php
    echo $this->Form->create('User', array(
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group'
            )
        ),
    ));
    echo $this->Form->input('current_pw', array(
        'label' => 'Current Password',
        'type' => 'password',
        'class' => 'form-control',
        'required' => false,
    ));
    echo $this->Form->input('password', array(
        'label' => 'New Password',
        'type' => 'password',
        'class' => 'form-control',
        'required' => false,
    ));
    echo $this->Form->input('confirm_pw', array(
        'label' => 'Confirm Password',
        'type' => 'password',
        'class' => 'form-control',
        'required' => false,
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