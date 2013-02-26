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
        $this->connection = mysql_connect( 
        	'localhost', 
        	'coby', 
        	'uL2YcVJPseU5XBBC');
        
        mysql_select_db('fangchia_budgetr');
    }
    
    function run( $sql ) {
        
        $result = mysql_query( $sql );

        $rows = array();

        if($result === false) {
            die('dead: ' . mysql_error() );
        }
        if($result === true ) {
            return true;
        }

        while( $row = mysql_fetch_assoc($result) ) {
            $rows[] = $row;
        }
        
        return $rows;
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