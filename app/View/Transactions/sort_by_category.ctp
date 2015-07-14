<div>
    <?php
    foreach ($listTransaction as $key => $listChild) :
        ?>
        <h3 class="clr-red"><img class="img-26px" src="<?php echo $listChild['category']['icon']; ?>"/> <?php echo $listChild['category']['name']; ?></h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Expense Type</th>
                        <th>Note</th>
                        <th colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($listChild['listTransaction'] as $key => $transaction) {
                        require 'list_sort_category.ctp';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>