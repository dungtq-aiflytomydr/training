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