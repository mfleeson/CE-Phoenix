<?php
/**
 * osCommerce Online Merchant - Phoenix
 * 
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license GNU General Public License; http://www.oscommerce.com/gpllicense.txt
 * @author Written for CE-Phoenix by Mark Fleeson mark@burninglight.co.uk 2020 
 * Release 1.1
 */

class ProductAttributes {
    private $_data = array();
    public function __construct( $name, $options, $products_options_id, $language_id ) {
        $this->_data = [];
        $this->_data[ $language_id ][ 'language_id' ] = $language_id;
        $this->_data[ $language_id ][ 'name' ] = $name;
        $this->_data[ $language_id ][ 'options' ] = $options;
        $this->_data[ $language_id ][ 'products_options_id' ] = $products_options_id;
    }
    public function getAttributeName() {
        return $this->_data[ $_SESSION['languages_id'] ][ 'name' ];
    }
    public function getAttributeOptions() {
        return $this->_data[ $_SESSION['languages_id'] ][ 'options' ];
    }
    public function getAttributeOptionsId() {

        return $this->_data[ $_SESSION['languages_id'] ][ 'products_options_id' ];
    }
}

?>