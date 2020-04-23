<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_options_attributes extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_OA_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $currencies, $l_product;
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
      $content_width = (int)MODULE_CONTENT_PI_OA_CONTENT_WIDTH;

		if($l_product->hasAttributes()) {
			$fr_input = $fr_required = '';
			if (MODULE_CONTENT_PI_OA_ENFORCE == 'True') {
			  $fr_input    = FORM_REQUIRED_INPUT;
			  $fr_required = 'required="required" aria-required="true" ';
			}

			$options = [];
			
			
			$attributes_data = $l_product->getAttributes(); // Gets an array of ProductAttributes, one entry for each set of attributes
			
			foreach($attributes_data as $attribute) {
				
			
				//Set up
				$options_output = null;
				$option_choices = [];
					
				if (MODULE_CONTENT_PI_OA_HELPER == 'True') {
					$option_choices[] = ['id' => '', 'text' => MODULE_CONTENT_PI_OA_ENFORCE_SELECTION];
          		}
				
				
				$poptions = $attribute->getAttributeOptions();
				
				foreach($poptions as $popt) {
					$text = $popt['products_options_values_name'];
					
					if ($popt['options_values_price'] != 0) {
				  		$text .= ' (' . $popt['price_prefix'] . $currencies->display_price( $popt['options_values_price'], tep_get_tax_rate($l_product->getTaxClass())) .') ';
					}
					$option_choices[] = ['id' => $popt['products_options_values_id'], 'text' => $text];
			  
			  	if (is_string($_GET['products_id'])) {
           		$selected_attribute = $_SESSION['cart']->contents[$_GET['products_id']]['attributes'][ $attribute->getAttributeOptionsId()] ?? false;
          		} else {
            		$selected_attribute = false;
          		
				}
				  
			}
				$options[] = [
					'id' => $attribute->getAttributeOptionsId(),
					'name' =>  $attribute->getAttributeName(),
					'choices' => $option_choices,
					'selection' => $selected_attribute,
				  ];
				}
		
        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      
		}

    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_OA_STATUS' => [
          'title' => 'Enable Options & Attributes',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_OA_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_OA_HELPER' => [
          'title' => 'Add Helper Text',
          'value' => 'True',
          'desc' => 'Should first option in dropdown be Helper Text?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_OA_ENFORCE' => [
          'title' => 'Enforce Selection',
          'value' => 'True',
          'desc' => 'Should customer be forced to select option(s)?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_OA_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '80',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

