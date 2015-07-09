<p>Dear <?php echo $name; ?>,</p>
<p>We provide for you new password.</p>
<p>Please click the link below to confirm this:</p>
<?php
echo $this->Html->link(
        'Confirm, i had a new password', array(
    'controller' => 'users',
    'action'     => 'reset_pw',
    $id,
    $forgot_pw_code,
    'full_base'  => true
));
