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
    ?>
    <div>
        <?php
        foreach ($listTransaction as $key => $listChild) :
            ?>
            <h3 class="clr-red"><?php
                if (date('d-m-Y', $listChild['create_time']) == date('d-m-Y', time())):
                    echo "Today (" . date('d-m-Y', $listChild['create_time']) . ")";
                else:
                    echo date('d-m-Y', $listChild['create_time']);
                endif;
                ?></h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Expense Type</th>
                            <th>Note</th>
                            <th colspan="3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($listChild['listTransaction'] as $key => $transaction) :
                            echo $this->element('transactions/list_sort_date', array(
                                'transaction' => $transaction,
                            ));
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    echo $this->element('transactions/show_statistical');
else:
    echo '<h3>Not found data :)</h3>';
    echo $this->Html->link('Add new transaction', array(
        'controller' => 'transactions',
        'action'     => 'add',
    ));
endif;
?>