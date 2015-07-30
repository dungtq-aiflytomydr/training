<tr>
    <td><?php echo ($key + 1); ?></td>
    <td><img class="img-26px" src="<?php
             if (!empty($wallet['Wallet']['icon'])) {
                 echo $wallet['Wallet']['icon'];
             } else {
                 echo '/img/wallet.png';
             }
             ?>"/></td>
    <td><?php echo $wallet['Wallet']['name']; ?></td>
    <td><?php echo __convertMoney($wallet['Wallet']['balance']); ?></td>
    <td><?php echo $wallet['Unit']['name'] . ' (' . $wallet['Unit']['signature'] . ')'; ?></td>
    <td><?php
        echo $this->Form->postLink(__('Select'), array(
            'action' => 'select',
            $wallet['Wallet']['id']), null, null);
        ?></td>
    <td><?php
        echo $this->Html->link('Edit', array(
            'controller' => 'wallets',
            'action'     => 'edit',
            $wallet['Wallet']['id']));
        ?></td>
    <td><?php
        echo $this->Form->postLink(__('Delete'), array('action' => 'delete',
            $wallet['Wallet']['id']), null, __('Are you sure you want to delete this item?'));
        ?></td>
</tr>