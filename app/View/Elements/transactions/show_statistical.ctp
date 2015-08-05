<div class="table-responsive popupForm">
    <h3>Statistical</h3>
    <hr/>
    <table class="table">
        <tbody>
            <tr>
                <td>Income</td>
                <td><?php echo __convertMoney($statisticalData['income']) . ' (' . $unitInfo['Unit']['signature'] . ')'; ?></td>
            </tr>
            <tr>
                <td>Expense</td>
                <td><?php echo __convertMoney($statisticalData ['expense']) . ' (' . $unitInfo['Unit']['signature'] . ')'; ?></td>
            </tr>
            <tr>
                <td></td>
                <td><hr/></td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?php echo __convertMoney($statisticalData['total']) . ' (' . $unitInfo['Unit']['signature'] . ')'; ?></td>
            </tr>
        </tbody>
    </table>
</div>