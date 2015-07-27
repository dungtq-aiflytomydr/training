<?php
echo $this->Html->script('categories/processCategory');

//process category icon
$icon = '/img/building.png';
if (!empty($catObj['Category']['icon'])) {
    $icon = $catObj['Category']['icon'];
}
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
        'default'  => $catObj['Category']['name'],
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
        'default'  => $catObj['Category']['expense_type'],
        'required' => false,
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