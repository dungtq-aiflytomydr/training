<?php
echo $this->Html->script('Transactions/processTransaction');

if (!empty($listTransaction)):

    require 'select_option_sort.ctp';
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
                            require 'list_sort_date.ctp';
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    require 'show_statistical.ctp';
else:
    echo '<h3>Not found data :)</h3>';
    echo $this->Html->link('Add new transaction', array(
        'controller' => 'transactions',
        'action'     => 'add',
    ));
endif;
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Transactions.init();
    });
</script>