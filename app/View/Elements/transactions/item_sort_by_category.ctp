<tr>
    <td><?php echo date('d-m-Y', $transaction['Transaction']['create_time']); ?></td>
    <td><?php echo __convertMoney($transaction['Transaction']['amount']); ?></td>
    <td><?php
        if ($transaction['Transaction']['category_info']['expense_type'] == 'in') :
            echo 'Income';
        else :
            echo 'Expense';
        endif;
        ?></td>
    <td><?php echo $transaction['Transaction']['note']; ?></td>
    <td><?php
        echo $this->Html->link('Edit', array(
            'controller' => 'transactions',
            'action'     => 'edit',
            $transaction['Transaction']['id'],
        ));
        ?></td>
    <td><?php
        echo $this->Form->postLink(__('Delete'), array('action' => 'delete',
            $transaction['Transaction']['id']), null, __('Are you sure you want to delete this item?'));
        ?></td>
</tr>