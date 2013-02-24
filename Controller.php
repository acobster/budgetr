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
        
        if( isset( $_GET['id'] ) ) {
            $id = $_GET['id'];
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
        
        if( empty( $this->data['message'] ) ) {
            $this->data['message'] = '';
        }

        $budget = isset( $_GET['budget'] )
            ? $_GET['budget']
            : 'monthly';

        switch( $budget ) {

            case 'monthly' :
                $this->data['content'] = $this->displayMonthly();
                break;

            case 'annual' :
                $this->data['content'] = $this->displayAnnual();
                break;

            default :
                $this->data['content'] = $this->displayMonthly();
        }

        $this->parseTemplate( 'budget' );
    }

    protected function displayMonthly()
    {
        $month = isset( $_GET['month'] )
            ? $_GET['month']
            : date('n');

        $items = $this->budget->fetchMonthlyItems( $month );
        $this->data['month'] = $month;

        $this->data['items'] = $items;
        $this->data['categories'] = $this->categorize( $items );
        $this->data['summary'] = $this->summarize( $items );

        $this->data['starting'] = $items[0]['total'];
        $this->data['total'] = $this->calculateTotal( $items );
        $this->data['remaining'] =
            $this->data['starting'] - $this->data['total'];

        return $this->parseTemplate( 'monthly', true );
    }

    protected function displayAnnual()
    {
        $this->debug('annual');
        $items = $this->budget->fetchAnnualItems();

        $this->data['items'] = $items;
        $this->data['categories'] = $this->categorize( $items );

        $this->data['starting'] = $items[0]['total'];
        $this->data['total'] = $this->calculateTotal( $items );
        $this->data['remaining'] =
            $this->data['starting'] - $this->data['total'];

        return $this->parseTemplate( 'annual', true );
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

    private function summarize( array $items ) {
        $parts = array( 0 => 0, 1 => 0 );
        foreach( $items as $item ) {
            $i = ( $item <= 5 or $item['day'] >= 19 )
                ? 0
                : 1;
            $parts[$i] += $item['amount'];
        }
        return $parts;
    }
    
    
    
    /***** View Methods *****/
    
    private function debug( $message = '' ) {
        if( $this->DEBUG ) {
            $this->data['debug'][] = $message;
        }
    }

    private function dump( $obj ) {
        $this->debug( '<pre>' . var_export( $obj, true ) . '</pre>' );
    }
    
    private function error( $message = '' ) {
        $this->errors[] = $message;
    }
    
    private function parseTemplate( $template, $return = false ) {

        foreach( $this->data as $k => $v ) {
            $$k = $v;
        }
        
        if( $return ) {
            ob_start();
            require 'templates/' . $template . '.php';   
            return ob_get_clean();
        } else {
            require 'templates/' . $template . '.php';   
        }
    }
    
    protected function formatAmt( $amt ) {
        return number_format( $amt/100, 2 );
    }

    private function catDropdownList( $cats, $item ) {

        $select = "<select name=\"item[{$item['id']}][catid]\">";

        foreach( $cats as $name => $cat ) {

            if( $cat['catid'] == $item['catid'] ) {
                $selected = 'selected';
                $class = 'class="sortField"';
            } else {
                $class = $selected = '';
            }
            
            $select .= <<<_HTML_
                <option value="{$cat['catid']}" $class $selected>
                    $name
                </option>
_HTML_;
        }

        $select .= '</select>';

        return $select;
    }

    private function dayDropdownList( $item ) {

        $select = "<select name=\"item[{$item['id']}][day]\">";

        foreach( range( 1, 31 ) as $day ) {

            if( $day == $item['day'] ) {
                $selected = 'selected';
            } else {
                $class = $selected = '';
            }
            
            $select .= <<<_HTML_
                <option value="{$cat['day']}" $selected>
                    $day
                </option>
_HTML_;
        }

        $select .= '</select>';

        return $select;
    }
}

