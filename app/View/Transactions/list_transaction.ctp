<?php
echo $this->Html->script('Transactions/processTransaction');

if (!empty($listTransaction)):
    ?>
    <h3 class="align-center">List transaction</h3>
    <div class="right">
        <h3 style="text-align: right; display: inline-block">Sort by: </h3>
        <select id="sortBy">
            <option value="sort_by_date" <?php
            if (strpos(Router::url(), 'sort_by_date') !== false) : echo 'selected';
            endif;
            ?>>Date</option>
            <option value="sort_by_category" <?php
            if (strpos(Router::url(), 'sort_by_category') !== false) : echo 'selected';
            endif;
            ?>>Category</option>
        </select>
    </div>
    <?php
    if (strpos(Router::url(), 'sort_by_date') !== false) :
        require 'sort_by_date.ctp';
    elseif (strpos(Router::url(), 'sort_by_category') !== false) :
        require 'sort_by_category.ctp';
    endif;
    ?>

    <h3 class="align-center"><?php
        echo $this->Html->link('Add new transaction', array(
            'controller' => 'transactions',
            'action'     => 'add',
        ));
        ?></h3>
    <!--statistical transactions-->
    <div class="table-responsive popupLogin">
        <h3>Statistical</h3>
        <hr/>
        <table class="table">
            <tbody>
                <tr>
                    <td>Income</td>
                    <td><?php echo $otherTransaction['income'] . ' (' . $otherTransaction['unit']['signature'] . ')'; ?></td>
                </tr>
                <tr>
                    <td>Expense</td>
                    <td><?php echo $otherTransaction['expense'] . ' (' . $otherTransaction['unit']['signature'] . ')'; ?></td>
                </tr>
                <tr>
                    <td>Balance</td>
                    <td><?php echo $otherTransaction['balance'] . ' (' . $otherTransaction['unit']['signature'] . ')'; ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td><hr/></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td><?php echo $otherTransaction['total'] . ' (' . $otherTransaction['unit']['signature'] . ')'; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
else:
    echo '<h3>You have not anything transaction.</h3>';
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