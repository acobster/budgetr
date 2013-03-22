<h2>Annual budget for <?= date( 'Y' ) ?></h2>

<form action="./?budget=annual" method="POST">

<p class="message <?= ($this->error) ? 'error' : '' ?>">
    <?= $message ?>
</p>

<p><button type="submit" name="action" value="save">Save</button></p>

<table id="budget" class="variations">
    <thead>
        <tr>
            <th class="budgetHeader">Name</th>
            <th class="budgetHeader">Description</th>
            <th class="budgetHeader">Category</th>
            <th class="budgetHeader">Month</th>
            <th class="budgetHeader">Day</th>
            <th class="budgetHeader">Amount</th>
            <th></th>
        </tr>
    </thead>

    <?php if( empty( $items ) ) : ?>
    <tr class="variation">
        <td class="itemName">            
            <input type="text" class="nameInp sortField"
            name="item[new][0][name]"
            value="" />
        </td>
        <td class="description">
            <input type="text" class="description sortField"
            name="item[new][0][description]"
            value="" />
        </td>
        <td class="category">
            <?= $this->catDropdownList( $categories, $item ) ?>
        </td>
        <td class="month">
            <input type="text" class="sortField"
            name="item[new][0][day]"
            value="" />
        </td>
        <td class="day">
            <input type="text" class="sortField"
            name="item[new][0][month]"
            value="" />
        </td>
        <td class="amount">
            $ <input type="text" name="item[new][0][amount]" value="" />
        </td>
        <td class="removeVar" title="Remove this budget item">
            <img src="images/remove.png" />
            <input type="hidden" name="itemid[]"
            value="" />
        </td>
    </tr>
    <?php else : ?>
    
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
        <td class="month">
            <input type="text" class="sortField"
            name="item[<?= $id ?>][month]"
            value="<?= $item['month'] ?>" />
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
    <?php endif; ?>

    <?= $tfoot ?>

</table>

<p><button type="submit" name="action" value="save">Save</button></p>

</form>


<aside>

<p><a href="./?view=budget&budget=monthly">View monthly budget</a></p>

<?= $this->parseTemplate( 'aside.categories' ) ?>

</aside>
