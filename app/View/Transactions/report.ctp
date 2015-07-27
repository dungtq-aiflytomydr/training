<?php
echo $this->element('transactions/select_option_sort');

if (!empty($listTransaction)) {

    function convert_money($money)
    {
        return number_format($money, 0, null, '.');
    }

    $unit = $statistical_data['unit']['signature'];

    echo $this->element('transactions/report_income', array(
        'unit' => $unit,
    ));
    ?>
    <hr/>
    <?php
    echo $this->element('transactions/report_expense', array(
        'unit' => $unit,
    ));
} else {
    echo '<h3>Not found data :)</h3>';
    echo $this->Html->link('Add new transaction', array(
        'controller' => 'transactions',
        'action'     => 'add',
    ));
}