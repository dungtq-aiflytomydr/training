<div>
    <?php
    foreach ($listTransaction as $key => $listChild) :
        ?>
        <h3 class="clr-red"><?php
            if (date('d-m-Y', $listChild['create_time']) == date('d-m-Y', time())):
                echo 'Today';
            else:
                echo date('d-m-Y', $listChild['create_time']);
            endif;
            ?></h3>
        <div class="table-responsive">
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
                    <?php
                    foreach ($listChild['listTransaction'] as $key => $transaction) {
                        require 'list_sort_date.ctp';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>