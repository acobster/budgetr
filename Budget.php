<?php

class Budget extends Model {

    public $name;
    public $id;
    
    protected $items;
    
    protected $DB;
    
    public function __construct( $id = 1 ) {
            
        $this->id = $id;
        
        $this->DB = DB::singleton();
    }
    
    public function fetchMonthlyItems( $month ) {

        $sql = "SELECT IF( i.id IS NULL, 'null', i.id ) id,"
            . " i.name, i.description, i.amount, i.day,"
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
            . " i.name, i.description, i.amount, i.day,"
            . " c.id catid, c.name category, c.description catdesc,"
            . " b.name budget, b.total"
            . " FROM items i JOIN categories c ON i.category = c.id"
            . " JOIN budget b ON i.budget = b.id"
            . " WHERE i.budget = {$this->id} AND i.month != 0"
            . " ORDER BY day";

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
        foreach( $data as $id => $row ) {
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
    }
    
    public function removeItems( $ids ) {
        Item::remove( $ids );
    }
}

