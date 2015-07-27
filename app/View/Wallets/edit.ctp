<?php echo $this->Html->script('wallets/processWallet'); ?>
<div class="popupForm">
    <?php
    //get list unit
    $optionUnit = array();
    foreach ($unitObj as $key => $value):
        $optionUnit[$value['Unit']['id']] = $value['Unit']['name'] . ' (' . $value['Unit']['signature'] . ')';
    endforeach;

    //process wallet's icon
    $icon = '/img/wallet.png';
    if (!empty($wallet['Wallet']['icon'])) {
        $icon = $wallet['Wallet']['icon'];
    }

    echo $this->Form->create('Wallet', array(
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group'
            )
        ),
        'type'          => 'file',
    ));
    echo $this->Form->input('name', array(
        'label'    => "Wallet's name",
        'class'    => 'form-control',
        'required' => false,
        'default'  => $wallet['Wallet']['name'],
    ));
    echo $this->Form->input('icon', array(
        'label'    => 'Icon',
        'type'     => 'file',
        'class'    => 'form-control',
        'between'  => "<div class='icon-preview form-group'><img class='wl-icon-preview' src='" . $icon . "'/></div>",
        'required' => false,
    ));
    echo $this->Form->input('unit_id', array(
        'options'  => array($optionUnit),
        'empty'    => 'Choose unit',
        'class'    => 'form-control',
        'required' => false,
        'default'  => $wallet['Wallet']['unit_id'],
    ));
    echo $this->Form->end(array(
        'label' => 'Update wallet',
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