<?php

class DB {

    private static $singleton;
    private $connection;

    static function singleton() {
        if( empty( self::$singleton ) ) {
            self::$singleton = new DB();
        }

        return self::$singleton;
    }

    protected function __construct() {

        $this->connection = new PDO(
            'mysql:dbname=fangchia_budgetr;host=localhost',
            'fangchia_budgetr',
            'uL2YcVJPseU5XBBC');
    }

    function run( $sql ) {

        $result = $this->connection->query( $sql );

        if($result === false) {
            $info = $this->connection->errorInfo();
            throw new RuntimeException( $info[2] );
        }

        $result = $result->fetchAll();
//echo '<pre>'.var_export($result,true).'</pre>';
        return $result;
    }

    function runSingle( $sql ) {

        $result = $this->run( $sql );

        if( count($result) > 1 ) {
            echo 'found multiple rows in ' . __FUNCTION__;
        }

        return $result[0];
    }
}

?>
