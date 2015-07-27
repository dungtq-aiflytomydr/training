<?php echo $this->Html->script('transactions/processTransaction'); ?>
<h3 class="align-center">List transaction</h3>
<div class="option-sort-area">
    <?php if (strpos(Router::url(), 'report') === false): ?>
        <div style="float: left">
            <?php
            echo $this->Html->link('View report', array(
                'controller' => 'transactions',
                'action'     => 'report',
                str_replace('-', '', $date_time),
            ));
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
        echo $this->Html->link('Back', array(
            'controller' => 'transactions',
            'action'     => 'listSortByDate',
            str_replace('-', '', $date_time),
        ));
    endif;
    ?>

    <div class="rp-box-date">
        <span>View by month: </span>
        <input id="rp-date" value="<?php echo $date_time; ?>"/>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Transactions.init();
    });
</script>