<div class="wl-content">
    <div class="align-center">
        <?php if ($walletObj['Wallet']['is_setup'] == NOT_SETUP): ?>
            <h3>Humm! This is first time, your account have use this an application.</h3>
            <h4>Setup your options to use: </h4>
            <hr/>
            <?php
            require 'add_wallet.ctp';
        else:
            ?>
            <h3 class="align-center">Your wallet have: <?php echo $walletObj['Wallet']['value']; ?> <?php echo $walletObj['Wallet']['unit']; ?></h3>
            <?php
            echo $this->Html->link('Option my wallet', array(
                'controller' => 'wallets',
                'action' => 'changeInfo'
            ));
        endif;
        ?>
    </div>
</div>