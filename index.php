<?php

require 'config.php';

ob_start();

// Timezone settings to avoid blah blah blah
$timezoneSetting = ini_get('date.timezone');
if( empty($timezoneSetting) ) {
  date_default_timezone_set('UTC');
}

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