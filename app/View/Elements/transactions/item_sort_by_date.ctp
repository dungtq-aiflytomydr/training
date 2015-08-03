<?php
//process icon
$icon = '/img/building.png';
if (!empty($transaction['Transaction']['category_info']['icon'])) :
    $icon = $transaction['Transaction']['category_info']['icon'];
endif;
?>
<tr>
    <td><img class="img-26px" src="<?php echo $icon; ?>" /></td>
    <td class="<?php
    if ($transaction['Transaction']['category_info']['expense_type'] == 'in'):
        echo 'clr-green';
    else:
        echo 'clr-red';
    endif;
    ?>"><?php echo $transaction['Transaction']['category_info']['name']; ?></td>
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