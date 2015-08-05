<?php
echo $this->element('transactions/select_option_sort');

/**
 * convert money (ex: 1000 => 1.000)
 * 
 * @param int $money
 * @return string
 */
function __convertMoney($money)
{
    return number_format($money, null, null, '.');
}

if (!empty($listTransaction)) {

    echo $this->element('transactions/report_income', array(
        'unit'        => $unitInfo,
        'statistical' => $statistical,
    ));
    ?>
    <hr/>
    <?php
    echo $this->element('transactions/report_expense', array(
        'unit'        => $unitInfo,
        'statistical' => $statistical,
    ));
} else {
    echo '<h3>Not found data :)</h3>';
}