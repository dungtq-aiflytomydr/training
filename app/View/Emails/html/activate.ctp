<p>Dear <?php echo $name; ?>,</p>
<p>Thank you for registering at Money Lover.</p>
<p>Please click the link below to activate your account:</p>
<?php
echo $this->Html->link(
        'Active your account', array(
    'controller' => 'users',
    'action'     => 'activate',
    $id,
    $active_code,
    'full_base'  => true
));
