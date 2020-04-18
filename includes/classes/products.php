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
	
	public function getCategoryProducts($catid)
	{
		$this->_data = [];
		
		if ( !empty($catid) ) {
			if ( is_numeric($catid) ) {
				/* Get products in categories */
				$products_query = tep_db_query("select products_id from products_to_categories where categories_id='".(int)$catid."';");
				while($products = tep_db_fetch_array($products_query)) {
					$this->_data[$products['products_id']] = new Product($products['products_id']);
				}
				
			}
		}
		return $this->_data;
	}
	
	public function getUpcomingProducts()
	{
		
		$this->_data = [];
		if(MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_FIELD == 'date_expected') 
		{ $l_sortfield = "products_date_available"; } 
		else { $l_sortfield = MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_FIELD;}
		
		$upcoming_query = tep_db_query("SELECT p.products_id from products p, products_description pd WHERE TO_DAYS(p.products_date_available) >= TO_DAYS(NOW()) AND pd.products_id = p.products_id AND pd.language_id = " . (int)$_SESSION['languages_id']. " ORDER BY " . $l_sortfield . " " . MODULE_CONTENT_UPCOMING_PRODUCTS_EXPECTED_SORT  . " LIMIT " . (int)MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY);
		while($products = tep_db_fetch_array($upcoming_query)) {
			$this->_data[$products['products_id']] = new Product($products['products_id']);
		}
		return $this->_data;
		
		
	}
	
	public function getData($key = null) { // Get everything stored about the product
		if ( isset($this->_data[$key]) ) {
			return $this->_data[$key];
		}
			
		return $this->_data;
	}
}

?>