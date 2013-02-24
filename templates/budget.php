<!DOCTYPE html>

<html>

<head>
<meta charset="utf-8" />
<title>Budgetr</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/tablesorter/style.css" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.variations.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<script type="text/javascript">

var numNewItems = 0;

function addBudgetItem(args) {
    with( $(args.variation) ) {

        // rename item inputs so the server knows they're new
        find('input').each( function() {
            with( $(this) ) {
                var name = prop( 'name' )
                	.replace( /item\[(new|null|\d*)\](\[\d+\])*/,
                    	'item[new]['+numNewItems+']' );
                prop( 'name', name );
            }
        });

        // put new item in correct category
        find('input.catid').prop('type', 'text').prop('placeholder', 'cat #');

		if( numNewItems == 0 ) {
			addClass( 'first' );
		} else {
			removeClass( 'first' );
		}

		addClass( 'newItem' );
    }
    numNewItems++;
}

function removeBudgetItem(args) {
    var id = $(args.variation).find('input[name^=itemid]').val();
    if( id ) {
        var input = '<input type="hidden" name="remove[]" value="'+id+'" />';
        $('#budget').after( input );
    }
}

$(document).ready(function() {

    registerVariations({});

    $( '#budget' ).tablesorter( { debug: true,
        textExtraction: function(node) {
            var val = $(node).children('.sortField').val();
            return val;
        }
    });
    
    $( 'form' ).submit( function() {
		return confirm( "Are you sure you want to save all changes?" );
    });
});

</script>
</head>

<body>

<div id="container">

<h1>Budgetr: <?= $d['budgetName'] ?></h1>

<form action="index.php" method="POST">

<p><?= $d['message'] ?></p>

<p><button type="submit" name="action" value="save">Save</button></p>

<input type="hidden" name="view" value="budget" />

<table id="budget" class="variations">
    <thead>
        <tr>
            <th class="budgetHeader">Name</th>
            <th class="budgetHeader">Description</th>
            <th class="budgetHeader">Category</th>
            <th class="budgetHeader" colspan="2">Amount</th>
        </tr>
    </thead>
    
    <?php foreach( $d['items'] as $item ) : ?>
    <tr class="variation">
        <td class="itemName">            
            <input type="text" class="nameInp sortField"
            name="item[<?= $item['id'] ?>][name]"
            value="<?= $item['name'] ?>" />
        </td>
        <td class="description">
            <input type="text" class="description sortField"
            name="item[<?= $item['id'] ?>][description]"
            value="<?= $item['description'] ?>" />
        </td>
        <td class="category">
            <?= $this->catDropdownList( $d['categories'], $item ) ?>
        </td>
        <td class="amount">
            $ <input type="text" class="sortField"
            name="item[<?= $item['id'] ?>][amount]"
            value="<?= $this->formatAmt( $item['amount'] ) ?>" />
        </td>
        <td class="removeVar" title="Remove this budget item">
            <img src="images/remove.png" />
            
            <input type="hidden" name="itemid[]"
            value="<?= $item['id'] ?>" />
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
    <tr class="budgetTotal amount" title="Budget total">
        <td colspan="2">Starting budget:</td>
        <td>$<?= $this->formatAmt( $d['starting'] ) ?></td>
        <td></td>
    </tr>
    <tr class="amount" title="Expenses total">
        <td colspan="2">Expenses:</td>
        <td>$<?= $this->formatAmt( $d['total'] ) ?></td>
        <td></td>
    </tr>
    <tr class="remaining amount" title="Budget total">
        <td colspan="2">Remaining:</td>
        <td>$<?= $this->formatAmt( $d['remaining'] ) ?></td>
        <td></td>
    </tr>
	</tfoot>

</table>

<p><button type="submit" name="action" value="save">Save</button></p>

</form>


<div id="categories">
	<h2>Subotals</h2>
    <p><a href="./?view=categories">Edit Categories</a></p>
    <table>
    	<tbody id="catList">
    	
    	<tr><th>Category</th><th>Total</th></tr>
    
        <?php foreach( $d['categories'] as $name => $cat ) : ?>
        <tr>
            <td colspan="2">
                <?= $name ?>
            </td>
            <td title="total for '<?= $name ?>'">
                    $<?= $this->formatAmt( $cat['subtotal'] ) ?>
            </td>
            <td></td>
        </tr>
        <?php endforeach; ?>

    	</tbody>
    </table>
</div>

</div><!-- /container -->

<?php if($d['debug']) : foreach( $d['debug'] as $debug ) : ?>
    <pre><?= $debug ?></pre>
<?php endforeach; endif; ?>

</body>

</html>