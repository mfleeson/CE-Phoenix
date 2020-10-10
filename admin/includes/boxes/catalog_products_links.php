<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/
/* Products Links - Allow linking of different products together and display
   (C) Mark Fleeson. mark@burninglight.co.uk 2020 
   v 0.1
*/
  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == BOX_HEADING_CATALOG ) {
      $group['apps'][] = array('code' => 'products_links.php',
                               'title' => MODULES_ADMIN_MENU_CATALOG_PRODUCTS_LINKS,
                               'link' => tep_href_link('products_links.php'));

      break;
    }
  }

