<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
/* Products Links - Allow linking of different products together and display
   (C) Mark Fleeson. mark@burninglight.co.uk 2020 
   v 0.1
*/

  class cm_pi_linked_products extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PRODUCT_INFO_LINKED_PRODUCTS_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $currencies, $PHP_SELF;

      $content_width = (int)MODULE_CONTENT_PRODUCT_INFO_LINKED_PRODUCTS_CONTENT_WIDTH;
      $card_layout = IS_PRODUCT_PRODUCTS_DISPLAY_ROW;

      $links_query = tep_db_query(<<<'EOSQL'

SELECT
  p.*, pd.*,
  IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
  IF(s.status, s.specials_new_products_price, p.products_price) AS final_price,
  p.products_quantity AS in_stock,
  IF(s.status, 1, 0) AS is_special
 FROM products_links pl
 INNER JOIN products p ON (p.products_id=pl.linked_products_id)
     LEFT JOIN specials s ON s.products_id = p.products_id
   LEFT JOIN products_description pd ON pd.products_id = p.products_id
 WHERE p.products_status = 1 AND pl.products_id =
EOSQL
        . (int)$_GET['products_id']
        . " AND pd.language_id = " . (int)$_SESSION['languages_id']
        . " ORDER BY p.products_price LIMIT " . (int)MODULE_CONTENT_PRODUCT_INFO_LINKED_PRODUCTS_CONTENT_LIMIT);
      $num_products_linked = tep_db_num_rows($links_query);

      if ($num_products_linked > 0) {
        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PRODUCT_INFO_LINKED_PRODUCTS_STATUS' => [
          'title' => 'Enable Linked Products Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_LINKED_PRODUCTS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PRODUCT_INFO_LINKED_PRODUCTS_CONTENT_LIMIT' => [
          'title' => 'Number of Products',
          'value' => '4',
          'desc' => 'How many products (maximum) should be shown?',
        ],
        'MODULE_CONTENT_PRODUCT_INFO_LINKED_PRODUCTS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '120',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }
	  
   public function install($parameter_key = null) {
      parent::install($parameter_key);

      tep_db_query(<<<'EOSQL'
CREATE TABLE products_links (  `products_id` int(11) NOT NULL,  `linked_products_id` int(11) NOT NULL,  `active` int(1) DEFAULT '1',
  PRIMARY KEY (`products_id`,`linked_products_id`) );
EOSQL
        );
    }

    public function remove() {
      parent::remove();

      tep_db_query("DROP TABLE products_links;");
    }

  }
