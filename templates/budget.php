<!DOCTYPE html>

<html>

<!-- TEST TEST TEST -->

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
        find('input, select').each( function() {
            with( $(this) ) {
                var name = prop( 'name' )
                	.replace( /item\[(new|null|\d*)\](\[\d+\])*/,
                    	'item[new]['+numNewItems+']' );
                prop( 'name', name );
            }
        });

        find('.itemMonth').val( 0 );

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

    registerVariations( {
        addCallback: 'addBudgetItem',
        beforeRemove: 'removeBudgetItem'
    } );

    $( '#budget' ).tablesorter( { debug: true,
        textExtraction: function(node) {
            var val = $(node).children('.sortField').val();
            if( ! val ) {
                val = $(node).children('.sortField option:selected').html();
            }
            return val;
        }
    });

    $('.refresh').click( function() {
        // don't submit any form data, just reload the URL
        window.location.href = window.location.href;
    });
    
    $( 'form' ).submit( function() {
		return confirm( "Are you sure you want to save all changes?" );
    });

    $('.day input').click(function() {
        $(this).select();
    });
});

</script>
</head>

<body>

<div id="container">
    
    <h1>Budgetr</h1>

    <?= $content ?>

</div><!-- /container -->

<?php if($debug) : foreach( $debug as $msg ) : ?>
    <pre><?= $msg ?></pre>
<?php endforeach; endif; ?>

</body>

</html>