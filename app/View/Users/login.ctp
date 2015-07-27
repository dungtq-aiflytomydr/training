<div class="popupForm">
    <?php
    echo $this->Session->flash('auth');

    echo $this->Form->create('User', array(
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group',
            ),
        ),
    ));

    echo $this->Form->input('email', array(
        'type'        => 'text',
        'label'       => 'Your email',
        'class'       => 'form-control',
        'required'    => false,
        'placeholder' => 'Your email',
    ));

    echo $this->Form->input('password', array(
        'label'       => 'Password',
        'class'       => 'form-control',
        'required'    => false,
        'placeholder' => 'Password',
    ));

    echo $this->Form->end(array(
        'label' => 'Login',
        'div'   => array(
            'class' => 'form-group',
        ),
        'class' => 'btn btn-default',
        'after' => $this->Html->link(' Forgot password?', array(
            'controller' => 'users',
            'action'     => 'forgotPwd',
        )),
    ));
    ?>
</div>