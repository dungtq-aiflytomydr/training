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
    $cateCompare    = 0;
    $flagCloseTable = 0;

    foreach ($listTransaction as $key => $tran) :
        if ($tran['Transaction']['category_id'] > $cateCompare) :
            if ($flagCloseTable > 0) :
                ?>
                </tbody>
                </table>
                </div>
                <?php
            endif;

            $cateCompare = $tran['Transaction']['category_id'];
            $classClr    = 'clr-red';
            if ($tran['Category']['expense_type'] == 'in') :
                $classClr = 'clr-green';
            endif;
            echo "<h3 class='{$classClr}'>{$tran['Category']['name']}</h3>";

            $flagCloseTable++;
            ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
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
                    echo $this->element('transactions/item_sort_by_category', array(
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
            