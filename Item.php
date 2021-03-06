<?php

class Item extends Model {
    
    protected $id;

    protected $name;
    protected $description;
    protected $amount;
    protected $catid;
    protected $budget;
    
    protected static $DB;
    
    protected static $validation = array(
        'name' => '/^[\w.,\- ]+$/',
        'description' => '/^[\w\.,\- ]+$/',
        'amount' => '/^\d+(\.\d\d)?$/',
        'catid' => '/^\d+$/',
        'budget' => '/^\d+$/',
    );

    public static function save( $id, $row ) {
        
        $row['amount'] = Budget::filterAmount( $row['amount'] );
        self::validate( $row );

        $sql = "UPDATE items SET name = '{$row['name']}',"
            . " description = '{$row['description']}',"
            . " budget = {$row['budget']},"
            . " amount = {$row['amount']},"
            . " category = {$row['catid']},"
            . " month = {$row['month']},"
            . " day = {$row['day']}"
            . " WHERE id = $id";

        DB::singleton()->run( $sql );
    }
    
    public static function create( $row ) {
        
        self::validate( $row );

        $amount = Budget::filterAmount( $row['amount'] );

        $sql = "INSERT INTO items SET name = '{$row['name']}',"
            . " description = '{$row['description']}',"
            . " budget = {$row['budget']},"
            . " amount = $amount,"
            . " category = {$row['catid']},"
            . " month = {$row['month']},"
            . " day = {$row['day']}";

        DB::singleton()->run( $sql );
    }
    
    public static function remove( $ids ) {
        
        $ids = array_unique( $ids );
        
        foreach( $ids as $id ) {
            if( ! filter_var( $id, FILTER_VALIDATE_INT ) ) {
                throw new RuntimeException( "Invalid item id: $id" );
            }
        }
        
        $ids = implode( ',', $ids );
        $sql = "DELETE FROM items WHERE id IN( $ids )";

        DB::singleton()->run( $sql );
    }
}