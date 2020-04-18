<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class bm_reviews {
    var $code = 'bm_reviews';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_BOXES_REVIEWS_TITLE;
      $this->description = MODULE_BOXES_REVIEWS_DESCRIPTION;

      if ( defined('MODULE_BOXES_REVIEWS_STATUS') ) {
        $this->sort_order = MODULE_BOXES_REVIEWS_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_REVIEWS_STATUS == 'True');

        $this->group = ((MODULE_BOXES_REVIEWS_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    function execute() {
      global $currencies, $oscTemplate;

     
		$random_select = "select r.reviews_id,r.products_id,substring(rd.reviews_text, 1, 60) as reviews_text, r.reviews_rating  from reviews r, reviews_description rd where r.reviews_status=1 and rd.reviews_id = r.reviews_id and rd.languages_id = '" . (int)$_SESSION['languages_id'] . "' order by r.reviews_id desc limit " . (int)MODULE_BOXES_REVIEWS_MAX_RANDOM_SELECT_REVIEWS;
		
		
		
		
	/*	$random_select = "select r.*, p.*, pd.*, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity as in_stock, if(s.status, 1, 0) as is_special, substring(reviews_text, 1, 60) as reviews_text ";
      $random_select .= "from reviews r, reviews_description rd, products p left join specials s on p.products_id = s.products_id, products_description pd ";
      $random_select .= "where p.products_status = '1' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)$_SESSION['languages_id'] . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' and r.reviews_status = 1 ";
      $random_select .= "order by r.reviews_id desc limit " . (int)MODULE_BOXES_REVIEWS_MAX_RANDOM_SELECT_REVIEWS;*/
      $random_product = tep_random_select($random_select);
      
      $box_attr = $box_title = $box_image = $box_price = $box_review_text = '';
      $data = array();

      if ($random_product) {
		  $l_product = new Product($random_product['products_id']);
		 
			$data['data-is-special'] = (int)$l_product->isSpecial();
			$data['data-product-price'] = $currencies->display_raw($l_product->getFinalPrice(), tep_get_tax_rate($l_product->getTaxClass()));
			$data['data-product-manufacturer'] = max(0, (int)$l_product->getManufacturersId());

		  // data attributes
        $box_attr = '';
			foreach ( $data as $key => $value ) {
			  $box_attr .= ' ' . tep_output_string_protected($key) . '="' . tep_output_string_protected($value) . '"';
			}

			$box_title = '<a href="' . tep_href_link('product_info.php', 'products_id=' . (int)$l_product->getID()) . '">' . $l_product->getTitle() . '</a>';
			$box_image = '<a href="' . tep_href_link('product_info.php', 'products_id=' . $l_product->getID()) . '">' . tep_image('images/' . $l_product->getImage(), htmlspecialchars($l_product->getTitle()), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, '', true, 'card-img-top') . '</a>';
			if ($l_product->isSpecial() == 1) {
			  $box_price = sprintf(IS_PRODUCT_SHOW_PRICE_SPECIAL, $currencies->display_price($l_product->getPrice(), tep_get_tax_rate($l_product->getTaxClass())), $currencies->display_price($l_product->getSpecialsPrice(), tep_get_tax_rate($l_product->getTaxClass())));
			} else {
			  $box_price = sprintf(IS_PRODUCT_SHOW_PRICE, $currencies->display_price($l_product->getPrice(), tep_get_tax_rate($l_product->getTaxClass())));
			}
			$box_review_text .= tep_draw_stars($random_product['reviews_rating']) . '<br>';
			$box_review_text .= tep_output_string_protected($random_product['reviews_text']) . '...';

			$tpl_data = ['group' => $this->group, 'file' => __FILE__];
			include('includes/modules/block_template.php');
		  }
	  
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_REVIEWS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Reviews Module', 'MODULE_BOXES_REVIEWS_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Selection of Random Reviews', 'MODULE_BOXES_REVIEWS_MAX_RANDOM_SELECT_REVIEWS', '10', 'How many records to select from to choose one random product review', '6', '2', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_BOXES_REVIEWS_CONTENT_PLACEMENT', 'Right Column', 'Should the module be loaded in the left or right column?', '6', '3', 'tep_cfg_select_option(array(\'Left Column\', \'Right Column\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_REVIEWS_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_REVIEWS_STATUS', 'MODULE_BOXES_REVIEWS_MAX_RANDOM_SELECT_REVIEWS', 'MODULE_BOXES_REVIEWS_CONTENT_PLACEMENT', 'MODULE_BOXES_REVIEWS_SORT_ORDER');
    }
  }
  