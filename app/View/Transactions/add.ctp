<?php
echo $this->Html->script('Transactions/processTransaction');

//format category for input select option category
$catSelect = array();

foreach ($listCategory as $key => $category) {
    $catSelect[$category['Category']['id']] = $category['Category']['name'];
}
?>
<div class="popupLogin">
    <?php
    echo $this->Form->create('Transaction', array(
        'inputDefaults' => array(
            'div' => array(
                'class' => 'form-group',
            )
        ),
    ));

    echo $this->Form->input('category_id', array(
        'label'    => 'Choose category',
        'options'  => array($catSelect),
        'empty'    => 'Choose Category',
        'class'    => 'form-control',
        'required' => false,
    ));

    echo $this->Form->input('amount', array(
        'type'     => 'text',
        'label'    => 'Money',
        'class'    => 'form-control',
        'required' => false,
    ));

    echo $this->Form->input('note', array(
        'type'     => 'textarea',
        'rows'     => 3,
        'style'    => 'resize: vertical;',
        'label'    => 'Note',
        'class'    => 'form-control',
        'required' => false,
    ));

    echo $this->Form->input('create_time', array(
        'type'     => 'text',
        'label'    => 'Time',
        'class'    => 'form-control',
        'required' => false,
    ));

    echo $this->Form->end(array(
        'label' => 'Save',
        'div'   => array(
            'class' => 'form-group',
        ),
        'class' => 'btn btn-default',
    ));
    ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Transactions.init();
    });
</script>