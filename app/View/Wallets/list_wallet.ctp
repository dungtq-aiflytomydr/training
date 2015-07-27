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
                    <th colspan="3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($listWallet as $key => $wallet):
                    echo $this->element('wallets/list_list_wallet', array(
                        'key'    => $key,
                        'wallet' => $wallet,
                    ));
                endforeach;
                ?>
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