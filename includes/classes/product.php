<?php
/**
 * osCommerce Online Merchant - Phoenix
 * 
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license GNU General Public License; http://www.oscommerce.com/gpllicense.txt
 * @author Written for CE-Phoenix by Mark Fleeson mark@burninglight.co.uk 2020 
 * Release 1.2
 */

class product {
    public $_data = array();

    public function __construct( $pID, $check = false ) {
        if ( !empty( $pID ) ) {
            if ( is_numeric( $pID ) ) {
                if ( $check == true ) {
                    $check_query = tep_db_query( "select * from products p where p.products_id='" . $pID . "' and p.products_status=1;" );
                    $rows_affected = tep_db_num_rows( $check_query );
                    return ( $rows_affected > 0 ? true : false );
                } else {
                    $this->buildProductdata( $pID );
                }
            }
        }

    }

    function buildProductdata( $pID ) {
        $this->_data = [];
        if ( !empty( $pID ) ) {
            if ( is_numeric( $pID ) ) {
                /* Get base product */
                $product_query = tep_db_query( "select * from products where products_id='" . ( int )$pID . "' and products_status='1';" );
                $selected_product = tep_db_fetch_array( $product_query );
                if ( tep_db_num_rows( $product_query ) === 1 ) {

                    $this->_data = array_merge( $this->_data, $selected_product );
                }

                /* Get product descriptions */
                $descriptions_query = tep_db_query( "select * from products_description where products_id='" . ( int )$pID . "';" );
                $ext_data = [];
                while ( $descriptions = tep_db_fetch_array( $descriptions_query ) ) {
                    $this->_data[ 'languages' ][ $descriptions[ 'language_id' ] ] = $descriptions;
                    if ( $descriptions[ 'language_id' ] == ( int )$_SESSION[ 'languages_id' ] ) {
                        $this->_data = array_merge( $this->_data, $descriptions );
                    }
                }

                /* Get product images */
                $images_query = tep_db_query( "select * from products_images where products_id='" . ( int )$pID . "';" );
                while ( $images = tep_db_fetch_array( $images_query ) ) {
                    $this->_data[ 'images' ][] = $images;
                }

                /* Get product categories */
                $categories_query = tep_db_query( "select categories_id from products_to_categories where products_id='" . ( int )$pID . "';" );
                while ( $categories = tep_db_fetch_array( $categories_query ) ) {
                    $this->_data[ 'categories' ][] = $categories;
                }

                /* Get product special pricing */
                $specials_query = tep_db_query( "select * from specials where products_id='" . ( int )$pID . "';" );
                $special = tep_db_fetch_array( $specials_query );
                $specials_data = [];
                $specials_data[ 'is_special' ] = 0;
                $specials_data[ 'specials_new_products_price' ] = 0;
                $specials_data[ 'expires_date' ] = '';

                if ( tep_db_num_rows( $product_query ) === 1 ) {
                    if ( $special[ 'status' ] === 1 ) {
                        $specials_data[ 'is_special' ] = 1;
                        $specials_data[ 'specials_new_products_price' ] = $special[ 'specials_new_products_price' ];
                        $specials_data[ 'specials_expires_date' ] = $special[ 'expires_date' ];
                    }
                }
                $this->_data = array_merge( $this->_data, $specials_data );


                $check_query = tep_db_query( "select COUNT(*) as co from products_attributes pa where pa.`products_id` ='" . ( int )$pID . "';" );
                $row = $check_query->fetch_row();

                $attributes_data = [];

                $product_attributes = [];
                if ( $row[ 0 ] > 0 ) {
                 
                    $attributes_data[ 'HasAttributes' ] = true;

                    $attributes_query = tep_db_query( "select distinct popt.* from products_options popt, products_attributes pa where 
pa.products_id ='" . ( int )$pID . "' AND pa.options_id = popt.`products_options_id`  ;" );
                    while ( $attributes = tep_db_fetch_array( $attributes_query ) ) {
                        $products_option_name = $attributes[ 'products_options_name' ];
                        $products_option_id = $attributes[ 'products_options_id' ];
                        $languageid = $attributes[ 'language_id' ];
                        $products_option_id = $attributes[ 'products_options_id' ];
                        $options_query = tep_db_query( "select pov.*,pa.* from products_attributes pa, products_options_values pov 
where pa.products_id ='" . ( int )$pID . "' AND pa.options_id = '" . ( int )$products_option_id . "' AND pa.options_values_id = pov.products_options_values_id" );
                        $product_options = [];
                        while ( $options = tep_db_fetch_array( $options_query ) ) {
                            $product_options[] = $options;
                        }

                        $l_attrib = new productattributes( $products_option_name, $product_options, $products_option_id, $languageid );

                        $product_attributes[] = $l_attrib;
                    }

                } else {
                    	$attributes_data[ 'HasAttributes' ] = false;
                }
                $attributes_data[ 'Attributes' ] = $product_attributes;
                $this->_data = array_merge( $this->_data, $attributes_data );
            }
        }

    }

    public function getData( $key = null ) { 
       return $this->_data[ $key ] ?? null;
    }
	public function getDebugData( ) { 
       return $this->_data;
    }
    public function getLanguageData( $key = null, $languages_id ) { // If a $key exists return that otherwise return full data array
        if ( isset( $this->_data[ 'languages' ][ $languages_id ][ $key ] ) ) {
            return $this->_data[ 'languages' ][ $languages_id ][ $key ];
        }

        return $this->_data[ 'languages' ][ $languages_id ];
    }

    public function isValid() { //Check we have data!
        return !empty( $this->_data );
    }

    public function hasData( $key ) {
        return ( isset( $this->_data[ $key ] ) && !empty( $this->_data[ $key ] ) );
    }

    public function isSpecial() {
        return ( isset( $this->_data[ 'is_special' ] ) && $this->_data[ 'is_special' ] == 1 );
    }

    public function inStock() {
        return ( $this->_data[ 'products_quantity' ] > 0 ? true : false );
    }

    public function getFinalPrice() {
        if ( $this->_data[ 'is_special' ] == 1 ) {
            return ( float )$this->_data[ 'specials_new_products_price' ];
        } else {
            return ( float )$this->_data[ 'products_price' ];
        }
    }

    public function hasPIImages() {
        if ( array_key_exists( 'images', $this->_data ) ) {
            return ( count( $this->_data[ 'images' ] ) > 0 ? true : false );
        } else {
            return false;
        }
    }

    public function getPIImages() {
        return $this->_data( 'images' );
    }

    public function hasAttributes() {
        return $this->_data[ 'HasAttributes' ];
    }

    public function getAttributes() {

        return $this->_data[ 'Attributes' ];
    }
	
    public function getAttributeCount() {

        return sizeof( $this->_data[ 'Attributes' ] );
    }
	
	/* Utility Functions */
	public function updateProductViewed() {
        global $languages_id;
        $sql = "update products_description set products_viewed = products_viewed+1 where products_id = '" . ( int )$this->_data[ 'products_id' ] . "' and language_id = '" . ( int )$languages_id . "'";
        return tep_db_query( $sql );
    }


}

?>