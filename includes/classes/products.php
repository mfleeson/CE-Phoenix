<?php
/**
 * osCommerce Online Merchant - Phoenix
 * 
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license GNU General Public License; http://www.oscommerce.com/gpllicense.txt
 * @author Written for CE-Phoenix by Mark Fleeson mark@burninglight.co.uk 2020 
 */

/* This class finds and holds lists of product */
class Products {

    protected $_data = array();

    public function __construct() {

        $this->_data = [];

    }

    /* Get a list of products for a category. As the product class only loads active products we don't need to search for active/inactive here.*/
    public function getCategoryProducts( $catid ) {
        $this->_data = [];

        if ( !empty( $catid ) ) {
            if ( is_numeric( $catid ) ) {
                /* Get products in categories */
                $products_query = tep_db_query( "select products_id from products_to_categories where categories_id='" . ( int )$catid . "';" );
                while ( $products = tep_db_fetch_array( $products_query ) ) {
                    $this->_data[ $products[ 'products_id' ] ] = new Product( $products[ 'products_id' ] );
                }

            }
        }
        return $this->_data;
    }

    /* Get a list of products that are coming soon. As the product class only loads active products we don't need to search for active/inactive here.*/
    public function getUpcomingProducts() {

        $this->_data = [];
        if ( MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_FIELD == 'date_expected' ) {
            $l_sortfield = "products_date_available";
        } else {
            $l_sortfield = MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_FIELD;
        }

        $upcoming_query = tep_db_query( "SELECT p.products_id from products p, products_description pd WHERE TO_DAYS(p.products_date_available) >= TO_DAYS(NOW()) AND pd.products_id = p.products_id AND pd.language_id = " . ( int )$_SESSION[ 'languages_id' ] . " ORDER BY " . $l_sortfield . " " . MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_SORT . " LIMIT " . ( int )MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY );
        while ( $products = tep_db_fetch_array( $upcoming_query ) ) {
            $this->_data[ $products[ 'products_id' ] ] = new Product( $products[ 'products_id' ] );
        }
        return $this->_data;


    }

    /* Get a list of products for a category. As the product class only loads active products we don't need to search for active/inactive here.*/
    public function getCardProducts() {

        $this->_data = [];


        $card_query = tep_db_query( "SELECT p.products_id from products p where p.products_status=1 ORDER BY p.products_id DESC LIMIT " . ( int )MODULE_CONTENT_CARD_PRODUCTS_MAX_DISPLAY );

        while ( $products = tep_db_fetch_array( $card_query ) ) {
            $this->_data[ $products[ 'products_id' ] ] = new Product( $products[ 'products_id' ] );
        }
        return $this->_data;


    }

    public function getCardProductsParent( $parent ) {

        $this->_data = [];


        $card_query = tep_db_query( "SELECT p.products_id from products p INNER JOIN products_to_categories p2c ON p.products_id = p2c.products_id INNER JOIN categories c ON p2c.categories_id = c.categories_id where p.products_status=1  and c.parent_id = " . ( int )$parent . " ORDER BY p.products_id DESC LIMIT " . ( int )MODULE_CONTENT_CARD_PRODUCTS_MAX_DISPLAY );

        while ( $products = tep_db_fetch_array( $card_query ) ) {
            $this->_data[ $products[ 'products_id' ] ] = new Product( $products[ 'products_id' ] );
        }
        return $this->_data;


    }

    public function getBestSellers( $current_category_id = 0 ) {
        $sql = "SELECT DISTINCT p.products_id FROM products p, products_description pd ";
        if ( $current_category_id > 0 ) {
            $sql .= ", products_to_categories p2c, categories c WHERE pd.products_id = p.products_id and p.products_id = p2c.products_id AND p2c.categories_id = c.categories_id AND "
                . ( int )$current_category_id . " IN (c.categories_id, c.parent_id) AND ";
        } else {
            $sql .= " WHERE ";
        }
        $sql .= " p.products_status = 1 AND p.products_ordered > 0  ORDER BY p.products_ordered DESC, pd.products_name LIMIT " . MODULE_BOXES_BEST_SELLERS_MAX_DISPLAY;

        $bestsellers_query = tep_db_query( $sql );

        while ( $products = tep_db_fetch_array( $bestsellers_query ) ) {
            $this->_data[ $products[ 'products_id' ] ] = new Product( $products[ 'products_id' ] );
        }
        return $this->_data;
    }

    public function getProductListing( $sql ) {
        $productlisting_query = tep_db_query( $sql );

        while ( $products = tep_db_fetch_array( $productlisting_query ) ) {
            $this->_data[ $products[ 'products_id' ] ] = new Product( $products[ 'products_id' ] );
        }

        return $this->_data;
    }


    public function sortProducts( $fieldname, $sortflags = SORT_REGULAR ) {
        $newarr = [];
        foreach ( $this->_data as $p ) {
            $newarr[ $p->getFieldname( $fieldname ) ] = $p;
        }
        sort( $newarr, $sortflags );
        $this->_data = $newarr;
    }


    public function getData( $key = null ) { // Get everything stored about the product
        if ( isset( $this->_data[ $key ] ) ) {
            return $this->_data[ $key ];
        }

        return $this->_data;
    }

    public function getCount() {
        return count( $this->_data );
    }


    /**********************************************
    Queries based on indiv products */

    public function GetWhatsNewProductId() {
        $random_select = "select p.products_id from products p where p.products_status = '1' order by products_date_added desc limit " . MODULE_BOXES_WHATS_NEW_MAX_RANDOM_SELECT_NEW;

        $random_product = tep_random_select( $random_select );

        return ( $random_product[ 'products_id' ] );

    }
}

?>