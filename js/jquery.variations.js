function registerVariations( args ) {
    
    // Default values for selectors, if necessary
    parent = ( args.parentSelector == undefined )
    ? '.variations'
    : args.parentSelector;

    child = ( args.childSelector == undefined )
    ? '.variation'
    : args.childSelector;

    addSelector = ( args.addSelector == undefined )
    ? '.addVar'
    : args.addSelector;

    removeSelector = ( args.removeSelector == undefined )
    ? '.removeVar'
    : args.removeSelector;

    varName = ( args.varNameSelector == undefined )
    ? '.varName'
    : args.varNameSelector;

    varOption = ( args.varOptionSelector == undefined )
    ? '.varOption'
    : args.varOption;

    // Destory old remove buttons, or create new ones accordingly
    $( parent ).each(function() {
        fixRemoveBtns(this);
    });

    regRemoveVariation();

    // add new variation
    $( parent+' '+addSelector ).click( function() {
        alert('add');

        var varParent = $(this).parents( parent );

        if( args.varHTMLCallback == undefined ) {
            var varHTML = varParent.find( child+':last' ).clone();
        } else {
            args.button = $(this);
            args.varParent = varParent;
            var varHTML = eval(args.varHTMLCallback)(args);
        }

        with (varParent) {
            // add a variation after the last current variation:
            find(child+':last').after(varHTML);

            // clear our new row's fields and focus on the first fievarHTMLld
            find(child+':last input').val('');
            find(child+':last input:first').focus();
            find(child+':last input:checkbox.varOption').removeAttr('checked');
        }

        fixRemoveBtns(varParent);

        // register remove button:
        regRemoveVariation();
        
        if( args.addCallback != undefined ) {
            var callbackArgs = {};
            callbackArgs.varParent = varParent;
            callbackArgs.variation = varParent.find( child+':last' );
            callbackArgs.varHTML = varHTML;
            callbackArgs.button = $(this);
            eval(args.addCallback)(callbackArgs);
        }

        return false;
    });


    function regRemoveVariation() {

        //remove variation
        $( removeSelector ).click(function() {
            
            if( args.beforeRemove != undefined ) {
                var callbackArgs = {};
                callbackArgs.varParent = $(this).parents( parent );
                callbackArgs.variation = $(this).parents( child );
                callbackArgs.button = $(this);
                eval(args.beforeRemove)(callbackArgs);
            }

            // all variations' parent:
            var varParent = $(this).parents( parent );
            // this one variation:
            var thisVar = $(this).parents( child );

            if(varParent.find( child ).size() > 1) {
                thisVar.remove();
            }

            fixRemoveBtns(varParent);
            regRemoveVariation();
            
            return false;
        });
    }

    // properly hide/show remove buttons on variations
    function fixRemoveBtns(vars) {
        with ($(vars)) {
            // if this set of variations contains only one variation...
            if(find( child ).size() <= 1) {
                // ...hide the remove button:
                find( child+' '+removeSelector ).hide();
            } else {
                // show all the buttons:
                find( child+' '+removeSelector ).show();
            }
        }
    }

    /*
      Play a nifty little trick in order to get checkbox data to POST correctly:
      Because variations are generic, we don't know what each one might be called.
      So, we'll use the value of the .varName field as the value for each checkbox.
     */
    if( $( 'form'+parent ) ) {
        $( 'form' ).submit( function() {
            $( child ).each( function() {
                with ($(this)) {
                    var name = find('input'+varName).val();
                    find('input:checkbox'+varOption).each( function() {
                        $(this).val(name);
                    });
                }
            });
            return true;
        });
    }
}