<!DOCTYPE html>

<html>

<head>
<meta charset="utf-8" />
<title>Budgetr</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.jeditable.js"></script>
<script type="text/javascript" src="js/jquery.variations.js"></script>
<script type="text/javascript">

var numNewItems = 0;

function addCategory(args) {
    with( $(args.variation) ) {

        // rename item inputs so the server knows they're new
        find('input').each( function() {
            with( $(this) ) {
                var name = prop( 'name' )
                	.replace( /categories\[(new|null|\d*)\](\[\d+\])*/,
                    	'categories[new]['+numNewItems+']' );
                prop( 'name', name );
            }
        });

		find('.catid').html('NEW');
    }
    numNewItems++;
}

function removeCategory(args) {
    var id = $(args.variation).find('input[name^=catid]').val();
    if( id ) {
        var input = '<input type="hidden" name="remove[]" value="'+id+'" />';
        $('.variations').after( input );
    }
}

$(document).ready(function() {
    registerVariations( {
        addCallback: 'addCategory',
        beforeRemove: 'removeCategory'
    } );
    
    $( 'form' ).submit( function() {
		return confirm( "Are you sure you want to save all changes?" );
    });
});
</script>
</head>

<body>

<div id="container">
    
    <h1>Budgetr: Categories</h1>
    
    <form action="index.php" method="POST">
    
    <p><?= $d['message'] ?></p>
    
    <a href="./?view=budget">Edit Budget</a>
    
    <p><button type="submit" name="action" value="save">Save</button></p>
    
    <input type="hidden" name="view" value="categories" />
    
    <table class="variations">
    	<tbody id="catList">
    	
    	<tr><th>Id</th><th>Name</th><th>Description</th></tr>
    	
    	<?php foreach( $d['categories'] as $cat ) : ?>
    	    <tr class="variation">
                <td class="catid"><?= $cat['id'] ?></td>
                <td>
                    <input type="text"
                    name="categories[<?= $cat['id'] ?>][name]"
                    value="<?= $cat['name'] ?>" />
                </td>
                <td>
                    <input type="text"
                    name="categories[<?= $cat['id'] ?>][description]"
                    value="<?= $cat['description'] ?>" />
                </td>
                <td class="removeVar" title="Remove this category">
                    <img src="images/remove.png" />
                    <input type="hidden" name="catid"
                    value="<?= $cat['id'] ?>" />
                </td>
    	    </tr>
    	<?php endforeach; ?>
    	
    	</tbody>
    	
    	<tfoot>
        <tr class="addVar" >
            <td colspan="4">
                <span title="Add a vategory">
                    <img src="images/plus.png" />
                    Add a category
                </span>
            </td>
        </tr>
    	</tfoot>

    </table>

    <p><button type="submit" name="action" value="save">Save</button></p>
    
    </form>

</div><!-- /container -->

<?php if($d['debug']) : foreach( $d['debug'] as $debug ) : ?>
    <pre><?= $debug ?></pre>
<?php endforeach; endif; ?>

</body>

</html>