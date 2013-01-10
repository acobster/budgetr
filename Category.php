<?php 

class Category extends Model {
    
    protected static $validation = array(
        'name' => '/^[\w ]+$/',
        'description' => '/^[\w\., ]+$/',
    );
    
    public static function fetch( $budget ) {

        if( ! filter_var( $budget, FILTER_VALIDATE_INT ) )
        {
            throw new RuntimeException("Invalid budget id: $budget" );
        }
        
        $sql = "SELECT * FROM categories WHERE budget = $budget";
        return DB::singleton()->run( $sql );
    }

    public static function save( $id, $row ) {

        self::validate( $row );
        
        $sql = "UPDATE categories SET name = '{$row['name']}',"
            . " description = '{$row['description']}'"
            . " WHERE id = $id";
        
        DB::singleton()->run( $sql );
    }
    
    public static function create( $row, $budget ) {
    
        if( ! filter_var( $budget, FILTER_VALIDATE_INT ) )
        {
            throw new RuntimeException("Invalid budget id: $budget" );
        }
        self::validate( $row );
        
        $sql = "INSERT INTO categories SET name = '{$row['name']}',"
            . " description = '{$row['description']}',"
            . " budget = $budget";
        
        DB::singleton()->run( $sql );
    }
    
    public static function remove( $ids ) {
        
        foreach( $ids as $id ) {
            if( ! filter_var( $id, FILTER_VALIDATE_INT ) )
            {
                throw new RuntimeException("Invalid category id: $id" );
            }
        }
        
        $ids = implode( ',', $ids );
        $sql = "DELETE FROM categories WHERE id IN( $ids )";
        
        DB::singleton()->run( $sql );
    }
}

?>