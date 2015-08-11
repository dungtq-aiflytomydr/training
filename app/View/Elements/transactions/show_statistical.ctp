<div class="table-responsive popupForm">
    <h3>Statistical</h3>
    <hr/>
    <table class="table">
        <tbody>
            <tr>
                <td>Income</td>
                <td><?php echo __convertMoney($statistical['totalIncome']) . ' (' . $unitInfo['Unit']['signature'] . ')'; ?></td>
            </tr>
            <tr>
                <td>Expense</td>
                <td><?php echo __convertMoney($statistical ['totalExpense']) . ' (' . $unitInfo['Unit']['signature'] . ')'; ?></td>
            </tr>
            <tr>
                <td></td>
                <td><hr/></td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?php echo __convertMoney($statistical['total']) . ' (' . $unitInfo['Unit']['signature'] . ')'; ?></td>
            </tr>
        </tbody>
    </table>
</div>