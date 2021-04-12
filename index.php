<?php

require 'config.php';

spl_autoload_register(function( string $class ) : void {
  if ( is_readable( "$class.php" ) ) {
    require "$class.php";
  }
});

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


?>
