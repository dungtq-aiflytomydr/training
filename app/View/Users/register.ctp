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
        'class' => 'form-control'
    ));
    echo $this->Form->input('password', array(
        'label' => 'Password',
        'class' => 'form-control'
    ));
    echo $this->Form->input('name', array(
        'label' => 'Name',
        'class' => 'form-control'
    ));
    echo $this->Form->end(array(
        'label' => 'Register',
        'div' => array(
            'class' => 'form-group'
        ),
        'class' => 'btn btn-default'));
    ?>
</div>