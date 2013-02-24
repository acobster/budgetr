<h2>Monthly budget for <?= date( 'F Y' ) ?></h2>

<form action="index.php" method="POST">

<p><?= $message ?></p>

<p><button type="submit" name="action" value="save">Save</button></p>

<input type="hidden" name="view" value="budget" />

<table id="budget" class="variations">
    <thead>
        <tr>
            <th class="budgetHeader">Name</th>
            <th class="budgetHeader">Description</th>
            <th class="budgetHeader">Category</th>
            <th class="budgetHeader">Day</th>
            <th class="budgetHeader">Amount</th>
            <th></th>
        </tr>
    </thead>
    
    <?php foreach( $items as $item ) : ?>
    <?php $id = $item['id']; ?>
    <tr class="variation">
        <td class="itemName">            
            <input type="text" class="nameInp sortField"
            name="item[<?= $id ?>][name]"
            value="<?= $item['name'] ?>" />
        </td>
        <td class="description">
            <input type="text" class="description sortField"
            name="item[<?= $id ?>][description]"
            value="<?= $item['description'] ?>" />
        </td>
        <td class="category">
            <?= $this->catDropdownList( $categories, $item ) ?>
        </td>
        <td class="day">
            <input type="text" class="sortField"
            name="item[<?= $id ?>][day]"
            value="<?= $item['day'] ?>" />
        </td>
        <td class="amount">
            $ <input type="text" class="sortField"
            name="item[<?= $id ?>][amount]"
            value="<?= $this->formatAmt( $item['amount'] ) ?>" />
        </td>
        <td class="removeVar" title="Remove this budget item">
            <img src="images/remove.png" />
            
            <input type="hidden" name="itemid[]"
            value="<?= $id ?>" />
        </td>
    </tr>
    <?php endforeach; ?>
        
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
        <td class="amount">$<?= $this->formatAmt( $starting ) ?></td>
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

</table>

<p><button type="submit" name="action" value="save">Save</button></p>

</form>


<aside>

<p>
    <a href="./?view=budget&budget=annual">View annual budget</a>
</p>

<?= $this->parseTemplate( 'aside.categories' ) ?>

<div id="summary">
    <h2>Summary</h2>
    <p class="instruct">Only items in <?= date('F') ?> are reflected.</p>
    <table>
        <tbody>
        
        <tr><th>Day range</th><th>Total</th></tr>
    
        <tr>
            <td><?= date('M') ?> 0-19</td>
            <td class="amount" title="total for '<?= $name ?>'">
                    $<?= $this->formatAmt( $summary[0] ) ?>
            </td>
        </tr>
        <tr>
            <td><?= date('M') ?> 20-31ish</td>
            <td class="amount" title="total for '<?= $name ?>'">
                    $<?= $this->formatAmt( $summary[0] ) ?>
            </td>
        </tr>

        </tbody>
    </table>
</div>

</aside>
