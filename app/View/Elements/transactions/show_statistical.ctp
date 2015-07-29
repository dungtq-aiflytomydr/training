<div class="table-responsive popupForm">
    <h3>Statistical</h3>
    <hr/>
    <table class="table">
        <tbody>
            <tr>
                <td>Income</td>
                <td><?php echo __convertMoney($statistical_data['income']) . ' (' . $statistical_data['unit']['signature'] . ')'; ?></td>
            </tr>
            <tr>
                <td>Expense</td>
                <td><?php echo __convertMoney($statistical_data['expense']) . ' (' . $statistical_data['unit']['signature'] . ')'; ?></td>
            </tr>
            <tr>
                <td>Balance</td>
                <td><?php echo __convertMoney($statistical_data['balance']) . ' (' . $statistical_data['unit']['signature'] . ')'; ?></td>
            </tr>
            <tr>
                <td></td>
                <td><hr/></td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?php echo __convertMoney($statistical_data['total']) . ' (' . $statistical_data['unit']['signature'] . ')'; ?></td>
            </tr>
        </tbody>
    </table>
</div>