<?php
require 'select_option_sort.ctp';

if (!empty($listTransaction)) {

    function convert_money($money)
    {
        return number_format($money, 0, null, '.');
    }

    $unit = $statistical_data['unit']['signature'];

    require 'report_income.ctp';
    ?>
    <hr/>
    <?php
    require 'report_expense.ctp';
} else {
    echo '<h3>Not found data :)</h3>';
    echo $this->Html->link('Add new transaction', array(
        'controller' => 'transactions',
        'action'     => 'add',
    ));
}