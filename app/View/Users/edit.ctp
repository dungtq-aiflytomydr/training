<?php
echo $this->Html->script('users/processUser');
$userAva = '/img/ava_default.jpeg';
if (!empty(AuthComponent::user('avatar'))) {
    $userAva = AuthComponent::user('avatar');
}
?>
<div class="popupForm">
    <?php
    echo $this->Form->create('User', array(
        'type'          => 'file',
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group'
            )
        )
    ));
    echo $this->Form->input('name', array(
        'label'    => 'Your name',
        'class'    => 'form-control',
        'required' => false,
    ));
    echo $this->Form->input('avatar', array(
        'type'     => 'file',
        'label'    => 'Avatar',
        'class'    => 'form-control',
        'between'  => "<div class='ava-preview form-group'><img class='u-ava-preview' src='" . $userAva . "'/></div>",
        'required' => false,
    ));
    echo $this->Form->input('address', array(
        'label'   => 'Address',
        'class'   => 'form-control',
        'rows'    => 3,
    ));
    echo $this->Form->end(array(
        'label' => 'Update profile',
        'div'   => array(
            'class' => 'form-group'
        ),
        'class' => 'btn btn-default'
    ));
    ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Users.init();
    });
</script>