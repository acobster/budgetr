<?php

class Controller {

    private $view;
    private $errors;

    private $data;

    private $DEBUG = true;

    function Controller( $view ) {
        $this->view = $view;
        $this->error = false;
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
                $this->budget->save( $_POST );
                $this->message( 'Saved successfully' );
            } catch( RuntimeException $e ) {
                $this->error( $e->getMessage() );
            }
        }

        if( empty( $this->data['message'] ) ) {
            $this->data['message'] = '';
        }

        $budget = isset( $_GET['budget'] )
            ? $_GET['budget']
            : 'monthly';

        switch( $budget ) {

            case 'annual' :
                $this->data['content'] = $this->displayAnnual();
                break;

            case 'monthly' :
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

        $this->data['starting'] = $items[0]['total'];

        $this->data['items'] = $items;
        $this->data['categories'] = $this->categorize( $items );
        $this->data['summary'] = $this->summarize( $items );

        $this->data['total'] = $this->calculateTotal( $items );
        $this->data['remaining'] =
            $this->data['starting'] - $this->data['total'];

        $this->data['tfoot'] = $this->parseTemplate( 'tfoot', true );

        return $this->parseTemplate( 'monthly', true );
    }

    protected function displayAnnual()
    {
        $items = $this->budget->fetchAnnualItems();

        $this->data['items'] = $items;
        $this->data['categories'] = $this->categorize( $items );

        $this->data['starting'] = $items[0]['total'];
        $this->data['total'] = $this->calculateTotal( $items );
        $this->data['remaining'] =
            $this->data['starting'] - $this->data['total'];

        $this->data['tfoot'] = $this->parseTemplate( 'tfoot', true );

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

        $this->data['categories'] = Category::fetch();
        $this->parseTemplate( 'categories' );
    }

    private function categorize( array $items ) {

        $cats = Category::fetch();

        $categorized = array();
        $total = 0;

        // categorize existing items
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

        // add categories without any items
        foreach( $cats as $cat ) {

            $name = $cat['name'];

            if( ! array_key_exists($name, $categorized) ) {
                $categorized[$cat['name']] = array(
                    'items'         => array(),
                    'total'         => 0,
                    'catid'         => $cat['id'],
                    'name'          => $name,
                    'description'   => $cat['description'],
                );
            }
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

        $summary = array( 0 => 0, 1 => 0 );
        $today = date('j');

        $summary['expenses'] = 0;

        foreach( $items as $item ) {
            // Past or future expense?
            $span = ( $item['day'] < $today )
                ? 'past'
                : 'future';

            // Sum up expenses in this time span
            $summary[$span] += $item['amount'];

            // // Sum up expenses for each pay period this month
            // $period = ( $item['day'] < 5 or $item['day'] >= 19 )
            //     ? 0
            //     : 0;
            // $summary[$period] += $item['amount'];

            // if( $period == $thisPeriod and $item['day'] >= $today )
            // {
            //     // Sum up remaining expenses for this period
            //     $summary['expenses'] += $item['amount'];
            // }
        }

        return $summary;
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
        $this->error = true;
        $this->message( $message );
    }

    private function message( $message = '' ) {
        $this->data['message'] = $message;
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

        $select = "<select class=\"sortField\"
        name=\"item[{$item['id']}][catid]\">";

        foreach( $cats as $name => $cat ) {

            if( $cat['catid'] == $item['catid'] ) {
                $selected = 'selected';
            } else {
                $selected = '';
            }

            $select .= <<<_HTML_
                <option value="{$cat['catid']}" $selected>
                    $name
                </option>
_HTML_;
        }

        $select .= '</select>';

        return $select;
    }

    private function dayDropdownList( $item ) {

        $select = "<select class=\"sortField\"
        name=\"item[{$item['id']}][day]\">";

        foreach( range( 1, 31 ) as $day ) {

            if( $day == $item['day'] ) {
                $selected = 'selected';
            } else {
                $selected = '';
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

