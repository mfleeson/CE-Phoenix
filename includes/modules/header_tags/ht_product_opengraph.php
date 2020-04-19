<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_product_opengraph extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_';

    public $group = 'header_tags';

    function execute() {
      global $PHP_SELF, $oscTemplate, $product_check, $currencies;

      //if ($product_check['total'] > 0) {
		if ($product_check == true) {
			
        /*$product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, p.products_image, p.products_price, p.products_quantity, p.products_tax_class_id, p.products_date_available from products p, products_description pd where p.products_id = " . (int)$_GET['products_id'] . " and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = " . (int)$_SESSION['languages_id']);

        if ( tep_db_num_rows($product_info_query) === 1 ) {
          $product_info = tep_db_fetch_array($product_info_query);*/
		
		  $l_product = new Product((int)$_GET['products_id']);
          $data = [
            'og:type' => 'product',
            'og:title' => $l_product->getTitle(),
            'og:site_name' => STORE_NAME,
          ];

          $product_description = substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($l_product->getDescription()))), 0, 197) . '...';
          $data['og:description'] = $product_description;

          $products_image = $l_product->getImage();
          //$pi_query = tep_db_query("select image from products_images where products_id = '" . (int)$product_info['products_id'] . "' order by sort_order limit 1");
          //if ( tep_db_num_rows($pi_query) === 1 ) {
          //  $pi = tep_db_fetch_array($pi_query);
             if ($l_product->hasPIImages()) {
           		$l_pi = $l_product->getPIImages();
			  
            	$products_image = $l_pi['images'];
          }
         // }
          $data['og:image'] = tep_href_link("images/$products_image", '', 'NONSSL', false, false);

        /*  if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
            $products_price = $currencies->display_raw($new_price, tep_get_tax_rate($product_info['products_tax_class_id']));
          } else {
            $products_price = $currencies->display_raw($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
          }*/
			$products_price = $currencies->display_raw($l_product->getFinalPrice(), tep_get_tax_rate($l_product->getTaxClass()));

          $data['product:price:amount'] = $products_price;
          $data['product:price:currency'] = $_SESSION['currency'];

          $data['og:url'] = tep_href_link('product_info.php', 'products_id=' . $l_product->getID(), 'NONSSL', false);

          $data['product:availability'] = ( $l_product->getQuantity() > 0 ) ? MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_TEXT_IN_STOCK : MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_TEXT_OUT_OF_STOCK;

          $result = '';
          foreach ( $data as $key => $value ) {
            $result .= '<meta property="' . tep_output_string_protected($key) . '" content="' . tep_output_string_protected($value) . '" />' . PHP_EOL;
          }

          $oscTemplate->addBlock($result, $this->group);
        }
      //}
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_STATUS' => [
          'title' => 'Enable Product OpenGraph Module',
          'value' => 'True',
          'desc' => 'Do you want to allow Open Graph Meta Tags (good for Facebook and Pinterest and other sites) to be added to your product page?  Note that your product thumbnails MUST be at least 200px by 200px.',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '900',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }