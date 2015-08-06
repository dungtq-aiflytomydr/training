<h3>Income (Total: <?php echo __convertMoney($statistical['totalIncome']) . ' ' . $unit['Unit']['signature']; ?>)</h3>
<h4>Maximum: <?php
    echo $statistical['maxIncome']['Category']['name'] .
    ' (' . __convertMoney($statistical['maxIncome']['Transaction']['amount']) . ' ' . $unit['Unit']['signature'] . ')'
    ?>
</h4>
<small style="display: block;">Transaction at: <?php echo date('Y-m-d', $statistical['maxIncome']['Transaction']['create_time']); ?></small>
<div class="rp-area">
    <?php
    foreach ($listTransaction as $key => $tran) :
        if ($tran['Category']['expense_type'] == 'in') :
            $width = round(($tran['sumMoney'] / $statistical['totalIncome'] * 100), 2);
            ?>
            <div class="rp-row row">
                <div class="rp-name col-md-3 col-xs-12">
                    <?php echo $tran['Category']['name'] . ' (' . __convertMoney($tran['sumMoney']) . ' ' . $unitInfo['Unit']['signature'] . ')'; ?>
                </div>
                <div class="rp-progress-bar col-md-8 col-xs-9">
                    <div class="progress">
                        <div class="progress-bar progress-bar-success" role="progressbar" 
                             aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $width; ?>%">
                        </div>
                    </div>
                </div>
                <div class="col-md-1 col-xs-3"><?php echo '(' . $width . '%)'; ?></div>
            </div>
            <?php
        endif;
    endforeach;
    ?>
</div>