<?php
echo $this->element('transactions/select_option_sort');

$totalIncome  = $totalExpense = 0;

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

if (!empty($listTransaction)):
    $dateCompare    = 253402189200; // <=> 9999-12-31
    $flagCloseTable = 0;

    foreach ($listTransaction as $key => $tran) :
        if ($tran['Transaction']['create_time'] < $dateCompare) :
            if ($flagCloseTable > 0) :
                ?>
                </tbody>
                </table>
                </div>
                <?php
            endif;

            $dateCompare = $tran['Transaction']['create_time'];
            echo "<h3 class='clr-red'>" . date('Y-m-d', $dateCompare) . "</h3>";

            $flagCloseTable++;
            ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Expense Type</th>
                            <th>Note</th>
                            <th colspan="3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    endif;

                    //render item_sort_by_date
                    echo $this->element('transactions/item_sort_by_date', array(
                        'tran' => $tran,
                    ));

                    if ($tran['Category']['expense_type'] == 'in') {
                        $totalIncome += $tran['Transaction']['amount'];
                    } else {
                        $totalExpense += $tran['Transaction']['amount'];
                    }

                endforeach;
                if ($flagCloseTable > 0) :
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    endif;

    $statisticalData = array(
        'income'  => $totalIncome,
        'expense' => $totalExpense,
        'total'   => $totalIncome - $totalExpense,
    );

    echo $this->element('transactions/show_statistical', array(
        'statisticalData' => $statisticalData,
        'unitInfo'        => $unitInfo,
    ));
else:
    echo '<h3>Not found data :)</h3>';
            endif;
            