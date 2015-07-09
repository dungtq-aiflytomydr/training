<?php if (!empty($listWallet)): ?>
    <h2>List wallet</h2>
    <hr/>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Stt</th>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Balance</th>
                    <th>Unit</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listWallet as $key => $wallet): ?>
                    <tr>
                        <td><?php echo ($key + 1); ?></td>
                        <td><img class="img-26px" src="<?php echo $wallet['Wallet']['icon']; ?>"/></td>
                        <td><?php echo $wallet['Wallet']['name']; ?></td>
                        <td><?php echo $wallet['Wallet']['balance']; ?></td>
                        <td><?php echo $wallet['Unit']['name'] . ' (' . $wallet['Unit']['signature'] . ')'; ?></td>
                        <td><?php
                            echo $this->Html->link('Edit', array(
                                'controller' => 'wallets',
                                'action'     => 'edit',
                                $wallet['Wallet']['id']));
                            ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
else:
    echo '<h3>You have not wallet.</h3>';
    echo $this->Html->link('Create new wallet.', array(
        'controller' => 'wallets',
        'action'     => 'add',
    ));
endif;