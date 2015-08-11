<?php
echo $this->element('transactions/lib_datepicker');
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

if (!empty($listTransaction)):
    $catCompare      = 0;
    $countCloseTable = 0;

    foreach ($listTransaction as $key => $tran) :
        if ($tran['Transaction']['category_id'] > $catCompare) :
            if ($countCloseTable > 0) :
                ?>
                </tbody>
                </table>
                </div>
                <?php
            endif;

            $catCompare = $tran['Transaction']['category_id'];
            $classClr   = 'clr-red';
            if ($tran['Category']['expense_type'] == 'in') :
                $classClr = 'clr-green';
            endif;
            echo "<h3 class='{$classClr}'>{$tran['Category']['name']}</h3>";

            $countCloseTable++;
            ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="200">Date</th>
                            <th width="300">Amount</th>
                            <th width="150">Expense Type</th>
                            <th width="250">Note</th>
                            <th colspan="2" width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    endif;

                    //render item_view_by_category
                    echo $this->element('transactions/item_view_by_category', array(
                        'tran'    => $tran,
                        'unitObj' => $unitInfo,
                    ));

                endforeach;
                if ($countCloseTable > 0) :
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    endif;
    //pagination
    echo $this->element('transactions/pagination');

    echo $this->element('transactions/show_statistical', array(
        'statistical' => $statistical,
        'unitInfo'    => $unitInfo,
    ));
else:
    echo '<h3>Not found data :)</h3>';
            endif;
            