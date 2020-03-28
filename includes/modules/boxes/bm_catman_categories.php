<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
/* Categories Manager - Suite to allow disabling categories and general management
   (C) Mark Fleeson. www.burninglight.co.uk 2020 
   v 0.1
*/
  class bm_catman_categories {
    var $code = 'bm_catman_categories';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
	var $depth;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_BOXES_CATMAN_CATEGORIES_TITLE;
      $this->description = MODULE_BOXES_CATMAN_CATEGORIES_DESCRIPTION;

      if ( defined('MODULE_BOXES_CATMAN_CATEGORIES_STATUS') ) {
        $this->sort_order = MODULE_BOXES_CATMAN_CATEGORIES_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_CATMAN_CATEGORIES_STATUS == 'True');
		$this->depth = MODULE_BOXES_CATMAN_CATEGORIES_DEPTH;
		$this->group = ((MODULE_BOXES_CATMAN_CATEGORIES_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    function execute() {
      global $oscTemplate, $cPath;
      $OSCOM_CategoryTree = new category_tree();
      $OSCOM_CategoryTree->setCategoryPath($cPath, '<strong>', '</strong>');
      $OSCOM_CategoryTree->setMaximumLevel($this->depth);
      
      $OSCOM_CategoryTree->setParentGroupString('<div class="list-group list-group-flush">', '</div>', false);
      $OSCOM_CategoryTree->setChildString('', '');
      
      $category_tree = $OSCOM_CategoryTree->getTree();
      
      $tpl_data = ['group' => $this->group, 'file' => __FILE__];
      include 'includes/modules/block_template.php';
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_CATMAN_CATEGORIES_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Categories Module', 'MODULE_BOXES_CATMAN_CATEGORIES_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_BOXES_CATMAN_CATEGORIES_CONTENT_PLACEMENT', 'Left Column', 'Should the module be loaded in the left or right column?', '6', '1', 'tep_cfg_select_option(array(\'Left Column\', \'Right Column\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_CATMAN_CATEGORIES_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
	  tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Category Depth', 'MODULE_BOXES_CATMAN_CATEGORIES_DEPTH', '0', 'How many levels of category to display.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_CATMAN_CATEGORIES_STATUS', 'MODULE_BOXES_CATMAN_CATEGORIES_CONTENT_PLACEMENT', 'MODULE_BOXES_CATMAN_CATEGORIES_SORT_ORDER','MODULE_BOXES_CATMAN_CATEGORIES_DEPTH');
    }
  }
