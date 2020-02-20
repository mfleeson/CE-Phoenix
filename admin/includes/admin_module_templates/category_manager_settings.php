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
*/
?>
<div class="col-sm-6 catman-settings-modal">
    <div class="modal fade" id="settings" >
     <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
             <h4 class="modal-title">HELLO</h4>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
				<P>Option 1 - Default Category Sort Order</P>
				<p>Option 2 - Default Site Starting Category</p>
				<P>Option 3 - </P>
             <div class="modal-footer">
            <?php /* <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button><?php echo tep_draw_button(MODULE_CONTENT_FOOTER_MODAL_CART_HEADING_TITLE . ($cart->count_contents() > 0 ? ' (' . $cart->count_contents() . ')' : ''), 'cart', tep_href_link('shopping_cart.php')) . tep_draw_button(IMAGE_BUTTON_CHECKOUT, 'fas fa-angle-right', tep_href_link('checkout_shipping.php', '', 'SSL')); */ ?>
				 Buttons
             </div>
            </div><!-- /.modal-body -->
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>   <!-- /.modal show-->
</div> <!-- /.catman-settings-modal-->