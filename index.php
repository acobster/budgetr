<?php

ob_start();

$income = 1000;

$view = isset( $_REQUEST['view'] )
    ? $_REQUEST['view']
    : 'monthly';

$control = new Controller( $view );
$control->execute();

ob_end_flush();


function __autoload( $class ) {
    require $class . '.php';
}

?>