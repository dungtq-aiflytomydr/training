<tr>
    <td><?php echo date('Y-m-d', $tran['Transaction']['create_time']); ?></td>
    <td><?php echo __convertMoney($tran['Transaction']['amount']) . " ({$unitInfo['Unit']['signature']})"; ?></td>
    <td><?php
        if ($tran['Category']['expense_type'] == 'in'):
            echo 'Income';
        else :
            echo 'Expense';
        endif;
        ?></td>
    <td><?php echo $tran['Transaction']['note']; ?></td>
    <td><?php
        echo $this->Html->link('Edit', array(
            'controller' => 'transactions',
            'action'     => 'edit',
            $tran['Transaction']['id'],
        ));
        ?></td>
    <td><?php
        echo $this->Form->postLink(__('Delete'), array('action' => 'delete',
            $tran['Transaction']['id']), null, __('Are you sure you want to delete this item?'));
        ?></td>
</tr>