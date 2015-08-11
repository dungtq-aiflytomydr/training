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

if (!empty($listTransaction)):
    $dateCompare     = 253402189200; // <=> 9999-12-31 (because datetime have a type timestamp)
    $countCloseTable = 0;

    foreach ($listTransaction as $key => $tran) :
        if ($tran['Transaction']['create_time'] < $dateCompare) :
            if ($countCloseTable > 0) :
                ?>
                </tbody>
                </table>
                </div>
                <?php
            endif;

            $dateCompare = $tran['Transaction']['create_time'];
            echo "<h3 class='clr-red'>" . date('Y-m-d', $dateCompare) . "</h3>";

            $countCloseTable++;
            ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="400">Name</th>
                            <th width="200">Amount</th>
                            <th width="150">Expense Type</th>
                            <th width="250">Note</th>
                            <th colspan="2" width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    endif;

                    //render item_view_by_date
                    echo $this->element('transactions/item_view_by_date', array(
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
            