<?php echo $this->Html->script('Wallets/processWallet'); ?>
<div class="popupLogin">
    <?php
    //get list unit
    $optionUnit = array();
    foreach ($unitObj as $key => $value):
        $optionUnit[$value['Unit']['id']] = $value['Unit']['name'] . ' (' . $value['Unit']['signature'] . ')';
    endforeach;

    echo $this->Form->create('Wallet', array(
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group'
            )
        ),
        'url'           => array(
            'controller' => 'wallets',
            'action'     => 'add'
        ),
        'type'          => 'file',
    ));
    echo $this->Form->input('name', array(
        'label'    => "Wallet's name",
        'class'    => 'form-control',
        'required' => false,
    ));
    echo $this->Form->input('icon', array(
        'label'   => 'Icon',
        'type'    => 'file',
        'class'   => 'form-control',
        'between' => "<div class='icon-preview form-group'><img class='wl-icon-preview' src='/img/wallet.png'/></div>",
    ));
    echo $this->Form->input('balance', array(
        'label'        => 'Setup value for your wallet<small> (Have you money?)</small>',
        'class'        => 'form-control',
        'autocomplete' => 'off',
        'required'     => false,
    ));
    echo $this->Form->input('unit_id', array(
        'options'  => array($optionUnit),
        'empty'    => 'Choose unit',
        'class'    => 'form-control',
        'required' => false,
    ));
    echo $this->Form->end(array(
        'label' => 'Create wallet',
        'div'   => array(
            'class' => 'form-group'
        ),
        'class' => 'btn btn-default',
    ));
    ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Wallets.init();
    });
</script>