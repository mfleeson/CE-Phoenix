<?php
/**
 * osCommerce Online Merchant - Phoenix
 * 
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license GNU General Public License; http://www.oscommerce.com/gpllicense.txt
 * @author Written for CE-Phoenix by Mark Fleeson mark@burninglight.co.uk 2020 
 */

class Product {
	
	protected $_data = array();
	
	public function __construct($id) {
		$this->_data = [];
		
		if ( !empty($id) ) {
			if ( is_numeric($id) ) {
				/* Get base product */
				$product_query = tep_db_query("select * from products where products_id='".(int)$id."' and products_status='1';");
				$selected_product = tep_db_fetch_array($product_query);
				if ( tep_db_num_rows($product_query) === 1 ) {	

					$this->_data = array_merge($this->_data,$selected_product);
				}
				
				/* Get product descriptions */
				$descriptions_query = tep_db_query("select * from products_description where products_id='".(int)$id."';");
				while ($descriptions = tep_db_fetch_array($descriptions_query)) {
					$this->_data['languages'][$descriptions['language_id']] = $descriptions;
				}
				
				/* Get product images */
				$images_query = tep_db_query("select * from products_images where products_id='".(int)$id."';");
				while($images = tep_db_fetch_array($images_query)) {
					$this->_data['images'][] = $images;
				}
				
				/* Get product categories */
				$categories_query = tep_db_query("select categories_id from products_to_categories where products_id='".(int)$id."';");
				while($categories = tep_db_fetch_array($categories_query)) {
					$this->_data['categories'][] = $categories;
				}
				
				/* Get product special pricing */
				$specials_query = tep_db_query("select * from specials where products_id='".(int)$id."';");
				$special = tep_db_fetch_array($specials_query); 
				$specials_data = [];
				$specials_data['is_special'] = 0;
				$specials_data['specials_new_products_price'] = 0;
						
				if ( tep_db_num_rows($product_query) == 1 ) {
					if($special['status'] == 1)
					{
						$specials_data['is_special'] = 1;
						$specials_data['specials_new_products_price'] = $special['specials_new_products_price'];
						
					}
				}
				$this->_data = array_merge($this->_data,$specials_data);
					
					
				
				/* Get product attributes */
			/*	$attributes_query = tep_db_query("select categories_id from products_to_categories where products_id='".(int)$id."';");
				while($attributes = tep_db_fetch_array($attributes_query)) {
					$this->_data['attributes'][$attributes['code']] = $attributes['value]';
				}*/
				//TO SORT ONCE KNOW WHATS NEEDED
				
				
				
				
			}
		}
		
	}
	
	public function isValid() { //Check we have data!
      	return !empty($this->_data);
    }
	
	public function getData($key = null) { // Get everything stored about the product
		if ( isset($this->_data[$key]) ) {
			return $this->_data[$key];
		}
			
		return $this->_data;
	}
	
	public function getID() { // Get products_id
      return $this->_data['products_id'];
    }
	
	public function getTitle() {
	  global $languages_id;	
      return $this->_data['languages'][$languages_id]['products_name'];
    }
	
	public function getDescription() {
	  global $languages_id;	
      return $this->_data['languages'][$languages_id]['products_description'];
    }

    public function hasModel() {
      return (isset($this->_data['model']) && !empty($this->_data['model']));
    }

    public function getModel() {
      return $this->_data['products_model'];
    }

    public function getQuantity() {
      return (int) $this->_data['products_quantity'];
    }
	
	public function getImage() {
      return $this->_data['products_image'];
    }
	
	public function getPrice() {
      return (float) $this->_data['products_price'];
    }
	
	public function getDateAdded() {
      return $this->_data['products_date_added'];
    }
	
	public function getLastModified() {
      return $this->_data['products_last_modified'];
    }
	
	public function getDateAvailable() {
      return $this->_data['products_date_available'];
    }
	
	public function getWeight() {
      return $this->_data['products_weight'];
    }
		
	public function getTaxClass() {
      return $this->_data['products_tax_class_id'];
    }
		
	public function getManufacturersId() {
      return $this->_data['manufacturers_id'];
    }
		
	public function getProductsOrdered() {
      return $this->_data['products_ordered'];
    }
		
	public function getGTIN() {
      return $this->_data['products_gtin'];
    }
	
	public function isSpecial() {
		return (isset($this->_data['is_special']) && $this->_data['is_special'] == 1);
	}
	
	public function inStock() {
		return ($this->_data['products_quantity'] > 0 ? true : false);
	}
	

	
	public function getSpecialsPrice() {
      return $this->_data['specials_new_products_price'];
    }
	
	public function getFinalPrice() {
		if($this->_data['is_special'] == 1) 
		{
			return $this->_data['specials_new_products_price'];
		}
		else
		{ return $this->_data['products_image'];} 
	}
}

?>