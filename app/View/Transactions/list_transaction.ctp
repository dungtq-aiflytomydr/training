<?php
if (!empty($listTransaction)):
    ?>
    <div class="table-responsive">
        <h3 class="align-center">List transaction</h3>
        <table class="table">
            <thead><tr>
                    <th>Icon</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Expense Type</th>
                    <th>Note</th>
                    <th colspan="3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listTransaction as $transaction): ?>
                    <tr>
                        <td><img class="img-26px" src="<?php echo $transaction['Transaction']['category_id']['icon']; ?>" /></td>
                        <td><?php echo $transaction['Transaction']['category_id']['name']; ?></td>
                        <td><?php echo $transaction['Transaction']['amount']; ?></td>
                        <td><?php
                            if ($transaction['Transaction']['category_id']['expense_type'] == 'in') {
                                echo 'Income';
                            } else {
                                echo 'Expense';
                            }
                            ?></td>
                        <td><?php
                            if (!empty($transaction['Transaction']['note'])):
                                echo $transaction['Transaction']['note'];
                            else:
                                echo 'None';
                            endif;
                            ?></td>
                        <td><?php
                            echo $this->Html->link('Edit', array(
                                'controller' => 'transactions',
                                'action'     => 'edit',
                                $transaction['Transaction']['id'],
                            ));
                            ?></td>
                        <td><?php
                            echo $this->Html->link('Delete', array(
                                'controller' => 'transactions',
                                'action'     => 'delete',
                                $transaction['Transaction']['id'],
                            ));
                            ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <h3 class="align-center"><?php
        echo $this->Html->link('Add new transaction', array(
            'controller' => 'transactions',
            'action'     => 'add',
        ));
        ?></h3>
    <div class="table-responsive popupLogin">
        <h3>Statistical</h3>
        <hr/>
        <table class="table">
            <tbody>
                <tr>
                    <td>Income</td>
                    <td><?php echo $otherTransaction['income']; ?></td>
                </tr>
                <tr>
                    <td>Expense</td>
                    <td><?php echo $otherTransaction['expense']; ?></td>
                </tr>
                <tr>
                    <td>Balance</td>
                    <td><?php echo $otherTransaction['balance']; ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td><hr/></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td><?php echo $otherTransaction['total']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
else:
    echo '<h3>You have not anything transaction.</h3>';
    echo $this->Html->link('Add new transaction', array(
        'controller' => 'transactions',
        'action'     => 'add',
    ));
endif;