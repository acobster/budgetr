<?php

class Controller {
    
    private $view;
    private $errors;
    
    private $data;
    
    private $DEBUG = true;
    
    function Controller( $view ) {
        $this->view = $view;
        $this->errors = array();
        $this->data = array();
        
        if( $this->DEBUG ) {
            $this->data['debug'] = array();
        }
    }
    
    function execute() {
        
        if( isset( $_GET['budget'] ) ) {
            $id = $_GET['budget'];
            if( ! filter_var( $id, FILTER_VALIDATE_INT ) ) {
                throw new RuntimeException( "Invalid budget id: $id" );
            }
        } else {
            $id = 1;
        }
        
        $this->budget = new Budget( $id );
        
        switch($this->view) {
            case 'categories' :
                $this->displayCategories();
                break;
                
            case 'budget' :
            default :
                $this->displayBudget();
                break;
        }
    }
    
    protected function displayBudget() {
        
        if( isset( $_POST['action'] ) && $_POST['action'] == 'save' ) {
            try {
                $this->budget->save( $_POST['item'] );
                $this->data['message'] = "Saved successfully";
            } catch( RuntimeException $e ) {
                $this->data['message'] = $e->getMessage();
            }
        
            if( $_POST['remove'] ) {
                try {
                    $this->budget->removeItems( $_POST['remove'] );
                    $this->data['message'] = "Saved successfully";
                } catch( RuntimeException $e ) {
                    $this->data['message'] = 'remove';
                }
            }
        }
        
        $items = $this->budget->fetchItems();

        $categories = $this->categorize( $items );
        
        $this->data['categories'] = $categories;
        
        $this->data['starting'] = $items[0]['total'];
        $this->data['total'] = $this->calculateTotal( $items );
        $this->data['remaining'] =
            $this->data['starting'] - $this->data['total'];
        
        $this->data['budgetName'] = ucwords( $this->budget->name );
        if( empty( $this->data['message'] ) ) {
            $this->data['message'] = '';
        }

        $this->parseTemplate( 'budget' );
    }
    
    protected function displayCategories() {
        
        if( isset( $_POST['action'] ) && $_POST['action'] == 'save' ) {
            try {
                
                foreach( $_POST['categories'] as $id => $cat ) {
                    
                    if( $id == 'new' ) {
                        foreach( $cat as $new ) {
                            Category::create( $new, $this->budget->id );
                        }
                    
                    } else {
                        Category::save( $id, $cat );
                        $this->data['message'] = 'Saved successfully';
                    }

                }
                
                if( $_POST['remove'] ) {
                    Category::remove( $_POST['remove'] );
                    $this->data['message'] = "Saved successfully";
                }
                
            } catch( RuntimeException $e ) {
                $this->data['message'] = $e->getMessage();
            }
        }
        
        $this->data['categories'] = Category::fetch( $this->budget->id );
        $this->parseTemplate( 'categories' );
    }
    
    private function categorize( array $items ) {
        
        $categorized = array();
        $total = 0;
        
        foreach( $items as & $item ) {
            
            $cat = $item['category'];
            
            if( ! array_key_exists($cat, $categorized) ) {
                $categorized[$cat] = array(
                	'items'         => array(),
                    'total'         => 0,
                    'catid'         => $item['catid'],
                    'name'	        => $item['category'],
                    'description'	=> $item['catdesc'],
                );
            }
            
            $categorized[$cat]['items'][] = $item;
        }
        
        return $this->calculateSubtotals( $categorized );
    }
    
    private function calculateTotal( array $items ) {
        $total = 0;
        foreach( $items as $item ) {
            $total += $item['amount'];
        }
        return $total;
    }
    
    private function calculateSubtotals( array $categories ) {
        foreach( $categories as & $cat ) {
            $total = 0;
            foreach( $cat['items'] as $item ) {
                $total += $item['amount'];
            }
            $cat['subtotal'] = $total;
        }
        return $categories;
    }
    
    protected function formatAmt( $amt ) {
        return number_format( $amt/100, 2 );
    }
    
    
    
    /***** View Methods *****/
    
    private function debug( $message = '' ) {
        if( $this->DEBUG ) {
            $this->data['debug'][] = $message;
        }
    }
    
    private function error( $message = '' ) {
        $this->errors[] = $message;
    }
    
    private function parseTemplate( $file ) {
        
        global $d;
        $d = $this->data;
        
        require_once 'templates/' . $file . '.php';
    }
}

