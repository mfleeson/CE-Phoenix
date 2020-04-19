<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_i_products_class_debug extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PRODUCTS_CLASS_DEBUG_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
		ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
      $content_width = MODULE_CONTENT_PRODUCTS_CLASS_DEBUG_CONTENT_WIDTH;



		//$l_product = new Product(4); //Shiny red apple
		$l_Products = new Products;
		$l_Products->getCardProducts();
	//	$l_Products->sortProducts('products_name');
//$l_Products->sortProducts('products_weight',SORT_NUMERIC);
        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
     //  include 'includes/modules/content/cm_template.php';
 
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PRODUCTS_CLASS_DEBUG_STATUS' => [
          'title' => 'Enable Products Class Debug Info Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PRODUCTS_CLASS_DEBUG_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PRODUCTS_CLASS_DEBUG_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '400',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
