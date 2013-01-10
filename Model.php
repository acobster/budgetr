<?php 

abstract class Model extends ArrayObject {
    protected static function validate( $row ) {
        foreach( static::$validation as $field => $pattern ) {
            if( ! preg_match( $pattern, $row[$field] )  ) {
                throw new RuntimeException( "Invalid $field: {$row[$field]}");
            }
        }
    }
    
    /***** ArrayAccess methods *****/
    
    public function offsetExists( $offset ) {
        return isset( $this->$offset );
    }
    
    public function offsetGet( $offset ) {
        if( $this->offsetExists( $offset ) ) {
            return $this->$offset;
        } else {
            throw new RuntimeException(
           		"Attempting to read undefined offset: $offset" );
        }
    }
    
    public function offsetSet( $offset, $value ) {
        if( $this->offsetExists( $offset ) ) {
            $this->$offset = $value;
        } else {
            throw new RuntimeException(
                "Attempting to write undefined offset: $offset" );
        }
    }
}

?>