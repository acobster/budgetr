<?php

class Budget extends Model {

    public $name;
    public $id;
    
    protected $items;
    
    protected $DB;

    protected static $validation = array(
        'total' => '/^\d+(\.\d\d)?$/',
    );
    
    public function __construct( $id = 1 ) {
            
        $this->id = $id;
        
        $this->DB = DB::singleton();
    }
    
    public function fetchMonthlyItems( $month ) {

        $sql = "SELECT IF( i.id IS NULL, 'null', i.id ) id,"
            . " i.name, i.description, i.amount, i.month, i.day,"
            . " c.id catid, c.name category, c.description catdesc,"
            . " b.name budget, b.total"
            . " FROM items i JOIN categories c ON i.category = c.id"
            . " JOIN budget b ON i.budget = b.id"
            . " WHERE i.budget = {$this->id} AND i.month IN( 0, $month )"
            . " ORDER BY day";
        
        $result = $this->DB->run( $sql );
        
        $this->name = $result[0]['budget'];
        
        return $result;
    }
    
    public function fetchAnnualItems() {

        $sql = "SELECT IF( i.id IS NULL, 'null', i.id ) id,"
            . " i.name, i.description, i.amount, i.month, i.day,"
            . " c.id catid, c.name category, c.description catdesc,"
            . " b.name budget, b.total"
            . " FROM items i JOIN categories c ON i.category = c.id"
            . " JOIN budget b ON i.budget = b.id"
            . " WHERE i.budget = {$this->id} AND i.month != 0"
            . " ORDER BY month, day";

        $result = $this->DB->run( $sql );
        
        $this->name = $result[0]['budget'];
        
        return $result;
    }

    public function fetchCategories() {

        $sql = "SELECT * FROM categories";

        $result = $this->DB->run( $sql );

        return $result;
    }
    
    public function save( $data ) {

        if( ! empty( $data['total'] ) ) {
            $this->saveTotal( $data['total'] );
        } else {
            throw new RuntimeException( 'No total specified!' );
        }

        foreach( $data['item'] as $id => $row ) {
            if( $id === 'new' ) {
                foreach( $row as $new ) {
                    $new['budget'] = $this->id;
                    Item::create( $new );
                }
            } elseif( $id !== 'null' ) {
                $row['budget'] = $this->id;
                Item::save( $id, $row );
            }
        }

        if( ! empty( $data['remove'] ) ) {
            $this->removeItems( $data['remove'] );
        }
    }
    
    public function removeItems( $ids ) {
        Item::remove( $ids );
    }

    public function saveTotal( $total ) {

        $total = self::filterAmount( $total );

        self::validate( array( 'total' => $total ) );

        $sql = "UPDATE budget SET total = $total";
        
        $result = $this->DB->run( $sql );
    }
    
    public static function filterAmount( $amt ) {

        $amt = str_replace( ',', '', $amt );

        if( ! preg_match( '/\.\d\d$/', $amt ) ) {
            $amt .= '.00';
        }
        return floatval( $amt ) * 100;
    }
}

