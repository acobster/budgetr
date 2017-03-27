<?php $theDate = DateTime::createFromFormat( 'n', $month ); ?>

<h2>Monthly budget for <?= $theDate->format( 'F' ) ?></h2>

<form action="./?month=<?= $theDate->format('n') ?>" method="POST">

<p class="message <?= ($this->errors) ? 'error' : '' ?>">
    <?= $message ?>
</p>

<p>
    <button type="submit" name="action" value="save">Save</button>
    <button type="button" class="refresh">Refresh</button>
</p>

<input type="hidden" name="theMonth" value="<?= $theDate->format('n') ?>" />

<table id="budget" class="variations budget">
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
        <td class="day">
            <input type="text" class="sortField"
            name="item[new][0][day]"
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
    <tr class="variation <?= $this->pastOrFuture($item) ?>">
        <td class="itemName" data-label="Name">
            <input type="text" class="nameInp sortField"
            name="item[<?= $id ?>][name]"
            value="<?= $item['name'] ?>" />
        </td>
        <td class="description" data-label="Description">
            <input type="text" class="description sortField"
            name="item[<?= $id ?>][description]"
            value="<?= $item['description'] ?>" />
        </td>
        <td class="category" data-label="Category">
            <?= $this->catDropdownList( $categories, $item ) ?>
        </td>
        <td class="day" data-label="Day">
            <input class="itemMonth"
            type="hidden" name="item[<?= $id ?>][month]"
            value="<?= $item['month'] ?>" />

            <input type="text" class="sortField"
            name="item[<?= $id ?>][day]"
            value="<?= $item['day'] ?>" />
        </td>
        <td class="amount" data-label="Amount">
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

<p>
    <button type="submit" name="action" value="save">Save</button>
    <button type="button" class="refresh">Refresh</button>
</p>

</form>


<aside>

<p>
    <a href="./?view=budget&budget=annual">View annual budget</a>
</p>
<p>
    <?php
    $date = clone $theDate;
    $prevMonth = $date->sub( new DateInterval('P1M') );

    $date = clone $theDate;
    $nextMonth = $date->add( new DateInterval('P1M') );
    ?>

    <a href="./?view=budget&month=<?= $prevMonth->format('n') ?>">
        View budget for <?= $prevMonth->format('F') ?></a>
</p>
<p>
    <a href="./?view=budget&month=<?= $nextMonth->format('n') ?>">
        View budget for <?= $nextMonth->format('F') ?></a>
</p>

<?= $this->parseTemplate( 'aside.categories' ) ?>

<div id="summary">
    <h2>Summary</h2>
    <table class="list">
        <tbody>

        <tr>
            <td>Past expenses:</td>
            <td class="amount">
                    $<?= $this->formatAmt( $summary['past'] ) ?>
            </td>
        </tr>
        <tr>
            <td>Remaining Expenses:</td>
            <td class="amount">$<?= $this->formatAmt( $summary['future'] ) ?></td>
        </tr>
        <tr class="remaining">
            <td>Spending:</td>
            <td class="amount">$<?= $this->formatAmt( $remaining ) ?></td>
        </tr>

        </tbody>
    </table>
    <p class="instruct">Only items for this
        <?= $theDate->format('F') ?> are reflected.</p>
</div>

</aside>
