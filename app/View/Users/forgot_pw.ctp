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
        'label' => 'Email had been registed',
        'class' => 'form-control'));
    echo $this->Form->end(array(
        'label' => 'Send me password',
        'div' => array(
            'class' => 'form-group'
        ),
        'class' => 'btn btn-default'
    ));
    ?>
</div>