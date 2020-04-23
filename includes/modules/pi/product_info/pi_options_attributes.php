<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class pi_options_attributes extends abstract_module {

    const CONFIG_KEY_BASE = 'PI_OA_';

    public $group = 'pi_modules_c';
    public $content_width;

    function __construct() {
      parent::__construct();

      $this->group = basename(dirname(__FILE__));

      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      $this->description .= '<div class="secInfo">' . cm_pi_modular::display_layout() . '</div>';

      if ( $this->enabled ) {
        $this->group = 'pi_modules_' . strtolower(PI_OA_GROUP);
        $this->content_width = (int)PI_OA_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      global $currencies, $l_product;

  /*    $products_options_name_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT DISTINCT popt.products_options_id, popt.products_options_name
  FROM products_options popt INNER JOIN products_attributes patrib ON patrib.options_id = popt.products_options_id
  WHERE patrib.products_id = %d AND popt.language_id = %d
  ORDER BY popt.products_options_name
EOSQL
        , (int)$_GET['products_id'], (int)$_SESSION['languages_id']));*/

      //if (tep_db_num_rows($products_options_name_query)) {
	  if($l_product->hasAttributes()) {
        $content_width = (int)PI_OA_CONTENT_WIDTH;

        $fr_input = $fr_required = '';
        if (PI_OA_ENFORCE == 'True') {
          $fr_input    = FORM_REQUIRED_INPUT;
          $fr_required = 'required="required" aria-required="true" ';
        }

        $tax_rate = tep_get_tax_rate($l_product->getTaxClass());

        $options = [];
		  
		$attributes_data = $l_product->getAttributes(); // Gets an array of ProductAttributes, one entry for each set of attributes
			
		foreach($attributes_data as $attribute) {  
		  
      //  while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
          $choices = [];

          if (PI_OA_HELPER == 'True') {
            $choices[] = ['id' => '', 'text' => PI_OA_ENFORCE_SELECTION];
          }

          /*$products_options_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix
 FROM products_attributes pa INNER JOIN products_options_values pov ON pa.options_values_id = pov.products_options_values_id
 WHERE pa.products_id = %d AND pa.options_id = %d AND pov.language_id = %d
EOSQL
            , (int)$_GET['products_id'], (int)$products_options_name['products_options_id'], (int)$_SESSION['languages_id']));
          while ($products_options = tep_db_fetch_array($products_options_query)) { */
			$poptions = $attribute->getAttributeOptions();
				
				foreach($poptions as $popt) {
					
            		$text = $popt['products_options_values_name'];
					
					if ($popt['options_values_price'] != '0') {
					  $text .= ' (' . $popt['price_prefix']
							 . $currencies->display_price($popt['options_values_price'], $tax_rate)
							 . ') ';
					}

            		$choices[] = ['id' => $popt['products_options_values_id'], 'text' => $text];
					
					if (is_string($_GET['products_id'])) {
						$selected_attribute = $_SESSION['cart']->contents[$_GET['products_id']]['attributes'][$attribute->getAttributeOptionsId()] ?? false;
					  } else {
						$selected_attribute = false;
					 }
				}

          

          $options[] = [
            'id' =>  $attribute->getAttributeOptionsId(),
            'name' => $attribute->getAttributeName(),
            'menu' => tep_draw_pull_down_menu(
                        'id[' . $attribute->getAttributeOptionsId() . ']',
                        $choices,
                        $selected_attribute,
                        $fr_required . 'id="input_' . $attribute->getAttributeOptionsId() . '"'
                      ),
          ];
        }

        $tpl_data = ['group' => $this->group, 'file' => __FILE__];
        include 'includes/modules/block_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'PI_OA_STATUS' => [
          'title' => 'Enable Options & Attributes',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_OA_GROUP' => [
          'title' => 'Module Display',
          'value' => 'C',
          'desc' => 'Where should this module display on the product info page?',
          'set_func' => "tep_cfg_select_option(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'], ",
        ],
        'PI_OA_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'PI_OA_HELPER' => [
          'title' => 'Add Helper Text',
          'value' => 'True',
          'desc' => 'Should first option in dropdown be Helper Text?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_OA_ENFORCE' => [
          'title' => 'Enforce Selection',
          'value' => 'True',
          'desc' => 'Should customer be forced to select option(s)?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_OA_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '310',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

