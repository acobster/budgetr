    <tfoot>
    <tr class="addVar" >
        <td colspan="4">
            <span title="Add a budget item">
                <img src="images/plus.png" />
                Add a budget item
            </span>
        </td>
    </tr>
    <tr class="budgetTotal" title="Budget total">
        <td colspan="4">Starting budget:</td>
        <td class="amount">
            $ <input name="total"
            value="<?= $this->formatAmt( $starting ) ?>" />
        </td>
        <td></td>
    </tr>
    <tr title="Expenses total">
        <td colspan="4">Expenses:</td>
        <td class="amount">$<?= $this->formatAmt( $total ) ?></td>
        <td></td>
    </tr>
    <tr class="remaining" title="Budget total">
        <td colspan="4">Remaining:</td>
        <td class="amount">$<?= $this->formatAmt( $remaining ) ?></td>
        <td></td>
    </tr>
	</tfoot>