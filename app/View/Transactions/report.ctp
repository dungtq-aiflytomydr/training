<?php echo $this->Html->script('Transactions/processTransaction'); ?>
<div class="rp-box-date">
    <input style="float:right;" id="rp-date"/>
    <h4 style="float: right;">Report by month: </h4>
</div>

<?php
if (!empty($listTransaction)) {

    function convert_money($money)
    {
        return number_format($money, 0, null, '.');
    }

    $unit = $statistical_data['unit']['signature'];

    require 'report_income.ctp';
    ?>
    <hr/>
    <?php
    require 'report_expense.ctp';
} else {
    echo '<h3>Không có dữ liệu :)</h3>';
}
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Transactions.init();
    });
</script>