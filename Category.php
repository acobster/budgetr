<?php 

class Category extends Model {
    
    protected static $validation = array(
        'name' => '/^[\w ]+$/',
        'description' => '/^[\w\., ]+$/',
    );
    
    public static function fetch() {
        
        $sql = "SELECT * FROM categories";
        return DB::singleton()->run( $sql );
    }

    public static function save( $id, $row ) {

        self::validate( $row );
        
        $sql = "UPDATE categories SET name = '{$row['name']}',"
            . " description = '{$row['description']}'"
            . " WHERE id = $id";
        
        DB::singleton()->run( $sql );
    }
    
    public static function create( $row ) {
        
        self::validate( $row );
        
        $sql = "INSERT INTO categories SET name = '{$row['name']}',"
            . " description = '{$row['description']}'";
        
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