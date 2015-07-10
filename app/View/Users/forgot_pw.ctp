<div class="popupLogin">
    <?php
    echo $this->Form->create('User', array(
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group',
            )
        )
    ));
    echo $this->Form->input('email', array(
        'type'     => 'text',
        'label'    => 'Your email',
        'class'    => 'form-control',
        'required' => false,
    ));
    echo $this->Form->end(array(
        'label' => 'Send me password',
        'div'   => array(
            'class' => 'form-group'
        ),
        'class' => 'btn btn-default',
    ));
    ?>
</div>