<div class="popupLogin">
    <?php
    echo $this->Form->create('User', array(
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group'
            )
        )
    ));
    echo $this->Form->input('email', array(
        'label' => 'Email',
        'class' => 'form-control',
        'type'  => 'text',
        'required' => false,
    ));
    echo $this->Form->input('password', array(
        'label' => 'Password',
        'class' => 'form-control',
        'required' => false,
    ));
    echo $this->Form->input('confirm_pw', array(
        'label' => 'Confirmation Password',
        'type'  => 'password',
        'class' => 'form-control',
        'required' => false,
    ));
    echo $this->Form->input('name', array(
        'label' => 'Name',
        'class' => 'form-control',
        'required' => false,
    ));
    echo $this->Form->end(array(
        'label' => 'Register',
        'div'   => array(
            'class' => 'form-group',
        ),
        'class' => 'btn btn-default'));
    ?>
</div>