<div class="col-sm-<?php echo $content_width; ?> cm-i-card-products">
  <h4><?php echo sprintf(MODULE_CONTENT_CARD_PRODUCTS_HEADING, strftime('%B')); ?></h4>

  <div class="<?php echo $card_layout; ?>">
    <?php
    $item = 1;
   // while ($card_products = tep_db_fetch_array($card_products_query)) {
	  foreach($l_Products->getData() as $l_product) {
      ?>
      <div class="card mb-2 is-product" data-is-special="<?php echo (int)$l_product->isSpecial(); ?>" data-product-price="<?php echo $currencies->display_raw($l_product->getFinalPrice(), tep_get_tax_rate($l_product->getTaxClass())); ?>" data-product-manufacturer="<?php echo max(0, (int)$l_product->getManufacturersId()); ?>">
        <a href="<?php echo tep_href_link('product_info.php', 'products_id=' . (int)$l_product->getID()); ?>"><?php echo tep_image('images/' . $l_product->getImage(), htmlspecialchars($l_product->getTitle()), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, '', true, 'card-img-top'); ?></a>
        <div class="card-body">         
          <h5 class="card-title">
            <a href="<?php echo tep_href_link('product_info.php', 'products_id=' . (int)$l_product->getID()); ?>"><?php echo $l_product->getTitle(); ?></a>
          </h5>
          <h6 class="card-subtitle mb-2 text-muted">
            <?php
            if ($l_product->isSpecial() == 1) {
              echo sprintf(IS_PRODUCT_SHOW_PRICE_SPECIAL, $currencies->display_price($l_product->getPrice(), $l_product->getTaxClass()), $currencies->display_price($l_product->getSpecialsPrice(), tep_get_tax_rate($l_product->getTaxClass())));
            }
            else {
              echo sprintf(IS_PRODUCT_SHOW_PRICE, $currencies->display_price($l_product->getPrice(), tep_get_tax_rate($l_product->getTaxClass())));
            }
            ?>
          </h6>          
        </div>
        <div class="card-footer bg-white pt-0 border-0">
          <div class="btn-group" role="group">
            <?php
            echo tep_draw_button(IS_PRODUCT_BUTTON_VIEW, '', tep_href_link('product_info.php', tep_get_all_get_params(array('action', 'products_id', 'sort', 'cPath')) . 'products_id=' . (int)$l_product->getID()), NULL, NULL, 'btn-info btn-product-listing btn-view') . PHP_EOL;
            $has_attributes = (tep_has_product_attributes((int)$l_product->getID()) === true) ? '1' : '0';
            if ($has_attributes == 0) echo tep_draw_button(IS_PRODUCT_BUTTON_BUY, '', tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action', 'products_id', 'sort', 'cPath')) . 'action=buy_now&products_id=' . (int)$l_product->getID()), NULL, array('params' => 'data-has-attributes="' . $has_attributes . '" data-in-stock="' . (int)$l_product->inStock() . '" data-product-id="' . (int)$l_product->getID() . '"'), 'btn-light btn-product-listing btn-buy') . PHP_EOL;
            ?>
          </div>
        </div>
      </div>
      <?php
      if ( $item%IS_PRODUCT_PRODUCTS_DISPLAY_ROW_SM == 0 ) echo '<div class="w-100 d-none d-sm-block d-md-none"></div>' . PHP_EOL; 
      if ( $item%IS_PRODUCT_PRODUCTS_DISPLAY_ROW_MD == 0 ) echo '<div class="w-100 d-none d-md-block d-lg-none"></div>' . PHP_EOL; 
      if ( $item%IS_PRODUCT_PRODUCTS_DISPLAY_ROW_LG == 0 ) echo '<div class="w-100 d-none d-lg-block d-xl-none"></div>' . PHP_EOL;
      if ( $item%IS_PRODUCT_PRODUCTS_DISPLAY_ROW_XL == 0 ) echo '<div class="w-100 d-none d-xl-block"></div>' . PHP_EOL;
      $item++;
    }
    ?>
  </div> 
</div>

<?php
/*
  Copyright (c) 2019, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
?>
