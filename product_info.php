<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!isset($_GET['products_id'])) {
    tep_redirect(tep_href_link('index.php'));
  }
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
  require('includes/languages/' . $language . '/product_info.php');

  $product_check = new Product((int)$_GET['products_id'],true); //tep_db_query("select count(*) as total from products p, products_description pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
 // $product_check = tep_db_fetch_array($product_check_query);

  require('includes/template_top.php');

  if ($product_check == false) {
?>

<div class="contentContainer">

  <div class="row">
    <?php echo $oscTemplate->getContent('product_info_not_found'); ?>
  </div>
  
</div>

<?php
  } else {
	  $l_product = new Product((int)$_GET['products_id']);
	 
  //  $product_info_query = tep_db_query("select p.*, pd.* from products p, products_description pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
   // $product_info = tep_db_fetch_array($product_info_query);

  //  tep_db_query("update products_description set products_viewed = products_viewed+1 where products_id = '" . (int)$_GET['products_id'] . "' and language_id = '" . (int)$languages_id . "'");
  $l_product->updateProductViewed();
?>

<?php echo tep_draw_form('cart_quantity', tep_href_link('product_info.php', tep_get_all_get_params(array('action')). 'action=add_product', 'NONSSL'), 'post', 'role="form"'); ?>

<?php
  if ($messageStack->size('product_action') > 0) {
    echo $messageStack->output('product_action');
  }
?>

<div class="contentContainer">

  <div class="row is-product">
    <?php echo $oscTemplate->getContent('product_info'); ?>
  </div>

</div>

</form>

<?php
  }
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
