<?php
echo $this->Html->script('categories/processCategory');

//process category icon
$icon = '/img/building.png';
if (!empty($this->request->data['Category']['icon'])) {
    $icon = $this->request->data['Category']['icon'];
}

//process option select wallet
$optionWallet = array();

foreach ($listWallet as $wallet) :
    $optionWallet[$wallet['Wallet']['id']] = $wallet['Wallet']['name'];
endforeach;
?>
<div class="popupForm">
    <?php
    echo $this->Form->create('Category', array(
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group'
            )
        ),
        'type'          => 'file',
    ));
    echo $this->Form->input('name', array(
        'label'    => "Category's name",
        'class'    => 'form-control',
        'required' => false,
    ));
    echo $this->Form->input('icon', array(
        'label'    => 'Icon',
        'type'     => 'file',
        'class'    => 'form-control',
        'between'  => "<div class='icon-preview form-group'><img class='wl-icon-preview' src='" . $icon . "'/></div>",
        'required' => false,
    ));
    echo $this->Form->input('expense_type', array(
        'options'  => array(
            'in'  => 'Income',
            'out' => 'Expense',),
        'empty'    => 'Choose type',
        'class'    => 'form-control',
        'required' => false,
    ));
    echo $this->Form->input('wallet_id', array(
        'label'   => 'Choose Wallet',
        'options' => $optionWallet,
        'class'   => 'form-control',
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
        Categories.init();
    });
</script>