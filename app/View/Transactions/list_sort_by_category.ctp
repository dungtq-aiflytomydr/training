<?php
echo $this->element('transactions/select_option_sort');

if (!empty($listTransaction)):
    ?>
    <div>
        <?php
        foreach ($listTransaction as $key => $listChild) :
            ?>
            <h3 class="<?php
            if ($listChild['category']['expense_type'] == 'in') {
                echo 'clr-green';
            } else {
                echo 'clr-red';
            }
            ?>"><img class="img-26px" src="<?php echo $listChild['category']['icon']; ?>"/> <?php echo $listChild['category']['name']; ?></h3>
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
                        foreach ($listChild['listTransaction'] as $key => $transaction) :
                            echo $this->element('transactions/list_sort_category', array(
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