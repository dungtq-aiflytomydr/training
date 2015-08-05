<?php
echo $this->Html->script('transactions/processTransaction');

if (empty($datetime)) {
    $datetime = date('Y-m', time());
}
?>
<h3 class="align-center">List transaction</h3>
<div class="option-sort-area">
    <?php if (strpos(Router::url(), 'report') === false): ?>
        <div style="float: left">
            <?php
            echo '<h4>' . $this->Html->link('View report', array(
                'controller' => 'transactions',
                'action'     => 'report',
                $datetime,
            )) . '</h4>';
            ?>
        </div>
        <div class="sort-by-box" style="float: right;">
            <span>Sort by: </span>
            <select id="sortBy" class="form-control" style="width: 120px; display: inline-block;">
                <option value="listSortByDate" <?php
                if (strpos(Router::url(), 'listSortByDate') !== false) : echo 'selected';
                endif;
                ?>>Date</option>
                <option value="listSortByCategory" <?php
                if (strpos(Router::url(), 'listSortByCategory') !== false) : echo 'selected';
                endif;
                ?>>Category</option>
            </select>
        </div>
        <?php
    else:
        echo '<h4>' . $this->Html->link('Back', array(
            'controller' => 'transactions',
            'action'     => 'listSortByDate',
            $datetime,
        )) . '</h4>';
    endif;
    ?>

    <div class="rp-box-date">
        <span>View by month: </span>
        <input style="display: inline-block" id="rp-date" value="<?php echo $datetime; ?>"/>
    </div>
</div>
<div>
    <h3><?php
        echo $this->Html->link('Add new transaction', array(
            'controller' => 'transactions',
            'action'     => 'add',
        ));
        ?></h3>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Transactions.init();
    });
</script>