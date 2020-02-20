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

 //CM
		/* Get Drop Down Select for New/Edit */

 foreach($category_work as $category) 
 { 
	 $selectvars[] = array('id'=>$category['categories_id'],'text'=>tep_output_string_protected($category['categories_name']));
 }

$product_sort_options[] = array('id'=>'Alphabetical','text'=>'Alphabetical');
$product_sort_options[] = array('id'=>'SortOrder','text'=>'Product Sort Order');
$product_sort_options[] = array('id'=>'NewestDesc','text'=>'Newest to Oldest');
$product_sort_options[] = array('id'=>'OldestDesc','text'=>'Oldest to Newest');

?>
<div class="col-sm-6 catman-newproduct-modal">
	<script>
function swopfocus() {
 	
  var x = document.getElementById("noparent").checked;
	
		document.getElementById("parent").disabled =  x;
	
}
function changeparent() {
  
  document.getElementById("noparent").checked =  0;
}
</script>

    <div class="modal fade" id="newProduct" >
     <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
             <h4 class="modal-title"><?php echo NEW_CATEGORY_FORM_TITLE; ?> </h4>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
				<!-- Created Tabbed Interface -->
				<ul class="nav nav-tabs" id="newTab" role="tablist"> <!-- start tabbed interface -->
<?php            
					
				 $category_inputs_string = $category_description_string = $category_seo_description_string = $category_seo_keywords_string = $category_seo_title_string  = $category_parent_string = $no_parent_string = $active_string = $product_sort_string ='';	
				 $languages = tep_get_languages();
					
				 for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
					 $tabline = "";
					 $tabline .= '<li class="nav-item">';
					 $active = ""; $selected="false";
					 $lcode = $languages[$i]['code'];
					 if($i == 0) 
					 {	$active="active";
					 	$selected="true";
					 };
					 $tabline .= '<a class="nav-link '.$active.'" id="base-tab-'.$lcode.'" data-toggle="tab" href="#base-'.$lcode.'" role="tab" aria-controls="base-'.$lcode.'" aria-selected="'.$selected.'">' . tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']).' </a>';
					 $tabline .= '</li>';
					  
					 echo $tabline;
					 
					 $category_inputs_string .=  ' '.tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']');
					 $category_description_string .= ' ' . tep_draw_textarea_field('categories_description[' . $languages[$i]['id'] . ']', 'soft', '70', '2');
          			 $category_seo_description_string .= ' ' . tep_draw_textarea_field('categories_seo_description[' . $languages[$i]['id'] . ']', 'soft', '70', '2');
          			 $category_seo_keywords_string .= ' ' . tep_draw_input_field('categories_seo_keywords[' . $languages[$i]['id'] . ']', NULL, 'style="width: 300px;" placeholder="' . PLACEHOLDER_COMMA_SEPARATION . '"');
          			 $category_seo_title_string .= ' ' . tep_draw_input_field('categories_seo_title[' . $languages[$i]['id'] . ']');
					 
					 $category_parent_string = tep_draw_pull_down_menu('parent',$selectvars,$selectvars[0]['text'],'id="parent" style="width:550px;overflow:hidden;white-space:pre;text-overflow:ellipsis;-webkit-appearance:none;" onclick="changeparent()"');
					 
					 $no_parent_string = tep_draw_selection_field('noparent', 'checkbox', '',true,'');
					 $no_parent_string = str_replace(">","",$no_parent_string);
					
					 $no_parent_string .= 'id="noparent" '.' onchange="swopfocus()">';
					 
					 $active_string = tep_draw_selection_field('active', 'checkbox', '',true,'');
					 
					 $product_sort_string = tep_draw_pull_down_menu('product_sort', $product_sort_options,$product_sort_options[0]['text'],'id="product_sort"  style="width:550px;overflow:hidden;white-space:pre;text-overflow:ellipsis;-webkit-appearance:none;" ');
					 
						 
			 }
			
				
?>
	  <li class="nav-item">
    <a class="nav-link" id="extras-tab" data-toggle="tab" href="#extras" role="tab" aria-controls="extras" aria-selected="false"><?PHP echo TAB_HEADER_EXTRA_CUSTOM_FIELDS; ?></a>
  </li>			
  
  <li class="nav-item">
    <a class="nav-link" id="other-tab" data-toggle="tab" href="#other" role="tab" aria-controls="other" aria-selected="false"><?PHP echo TAB_HEADER_FIXED_VALUE_FIELDS; ?></a>
	 
  </li>

</ul>
				
<?php echo tep_draw_form('newcategory', 'categories_manager.php', 'action=insert_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"'); 
		
/* Set up bootstrap row/column variables */
		$layout_row_begin_col1 = '<div class="row"><div class="col-md-3">';
		$layout_begin_col2 = '<div class="col-md-9">';
		$layout_begin_col2_skip_col1 = '<div class="row"><div class="col-md-3">&nbsp;</div><div class="col-md-9">';
		$layout_end_col1 = '</div>';
		$layout_end_col2 = '</div></div>';
		$lb = "<br><br>";		
				
				
?>
<div class="tab-content" id="myTabContent">
	<?php
	for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		$active = "";
		$lcode = $languages[$i]['code'];
		if($i == 0) 
		{	$active="active";
			$selected="true";
		};
		 echo '<div class="tab-pane fade show active" id="base-'.$lcode.'" role="tabpanel" aria-labelledby="base-tab-'.$lcode.'">';
	
		
			echo '<div class="container">';
		echo '<div class="row"><div class="col-md-12">'.TEXT_NEW_CATEGORY_INTRO.$lb.'</div></div>';
		
		echo $layout_row_begin_col1.TEXT_CATEGORIES_NAME.$layout_end_col1;
		echo $layout_begin_col2.$category_inputs_string.TEXT_CATEGORIES_NO_PARENT_TITLE.$no_parent_string.$lb.$layout_end_col2;
		
		echo $layout_row_begin_col1.TEXT_CATEGORIES_PARENT_TITLE.$layout_end_col1;
		echo $layout_begin_col2.$category_parent_string.$layout_end_col2;
		echo $layout_begin_col2_skip_col1.TEXT_CATEGORIES_PARENT_TITLE_COMMENT.$lb.$layout_end_col2;
		
		echo $layout_row_begin_col1.TEXT_CATEGORIES_SEO_TITLE.$layout_end_col1;
		echo $layout_begin_col2.$category_seo_title_string.$layout_end_col2;
		echo $layout_begin_col2_skip_col1.TEXT_CATEGORIES_SEO_TITLE_COMMENT.$lb.$layout_end_col2;
		
		echo $layout_row_begin_col1.TEXT_CATEGORIES_DESCRIPTION.$layout_end_col1;
		echo $layout_begin_col2.$category_description_string.$layout_end_col2;
		echo $layout_begin_col2_skip_col1.TEXT_CATEGORIES_DESCRIPTION_COMMENT.$lb.$layout_end_col2;
		
	
		echo $layout_row_begin_col1.TEXT_CATEGORIES_SEO_DESCRIPTION.$layout_end_col1;
		echo $layout_begin_col2.$category_seo_description_string.$layout_end_col2;
		echo $layout_begin_col2_skip_col1.TEXT_CATEGORIES_SEO_DESCRIPTION_COMMENT.$lb.$layout_end_col2;
		  
		echo $layout_row_begin_col1.TEXT_CATEGORIES_SEO_KEYWORDS.$layout_end_col1;
		echo $layout_begin_col2.$category_seo_keywords_string.$layout_end_col2;
		echo $layout_begin_col2_skip_col1.TEXT_CATEGORIES_SEO_KEYWORDS_COMMENT.$lb.$layout_end_col2;
		
		echo $layout_row_begin_col1.TEXT_CATEGORIES_IMAGE.$layout_end_col1;
		echo $layout_begin_col2.tep_draw_file_field('categories_image').$lb.$layout_end_col2;
		
	
		
		echo $layout_row_begin_col1.TEXT_CATEGORIES_SORT_ORDER.$layout_end_col1;
		echo $layout_begin_col2.tep_draw_input_field('sort_order', '0', 'size="2"','number').$lb.$layout_end_col2;
		
		echo $layout_row_begin_col1.TEXT_PRODUCTS_SORT_ORDER_TITLE.$layout_end_col1;
		echo $layout_begin_col2.$product_sort_string.$layout_end_col2;
		echo $layout_begin_col2_skip_col1.TEXT_PRODUCTS_SORT_ORDER_TITLE_COMMENT.$lb.$layout_end_col2;
       
       
       echo $layout_row_begin_col1.TEXT_CATEGORIES_ACTIVE_TITLE.$layout_end_col1;
		echo $layout_begin_col2.$active_string.$layout_end_col2;
		echo $layout_begin_col2_skip_col1.TEXT_CATEGORIES_ACTIVE_TITLE_COMMENT.$lb.$layout_end_col2;
       
		?>
		</div>
	</div>
<?php
	}
?>
	<script>
			swopfocus();
	</script>
 
  <div class="tab-pane fade" id="extras" role="tabpanel" aria-labelledby="extras-tab">
	     <div class="container">
		<div class="row"><div class="col-md-12"><?PHP echo $lb; ?>
			</div></div>
		
	<?php
	  foreach($catmanfields as $catman) {
		  switch ($catman) {
			  case 'active':
				  break;
			  case 'products_sort':
				  break;
			  default:
				  echo $layout_row_begin_col1.$catman.$layout_end_col1;
				  echo $layout_begin_col2.tep_draw_input_field($catman, '', '').$lb.$layout_end_col2;
		  }
			  
		 
	  }
	  ?>
	 </div>
	
			</div>
			
			  <div class="tab-pane fade" id="other" role="tabpanel" aria-labelledby="other-tab">
	   
	    
	   <div class="container">
		<div class="row"><div class="col-md-12"><?PHP echo TAB_HEADER_FIXED_VALUE_FIELDS_COMMENT.$lb ?>
			</div></div>
		
		<?php 
	
			echo $layout_row_begin_col1.TEXT_CATEGORIES_ID.$layout_end_col1;
			echo $layout_begin_col2.tep_draw_input_field('categories_id', 'To be set on save', ' disabled ','',' disabled ').$lb.$layout_end_col2;
	   		$now = date("Y/m/d h:i:s");
			echo $layout_row_begin_col1.TEXT_WHEN_ADDED.$layout_end_col1;
			echo $layout_begin_col2.tep_draw_input_field('date_added',$now,' disabled ').$lb.$layout_end_col2;
	   
	   		echo $layout_row_begin_col1.TEXT_WHEN_MODIFIED.$layout_end_col1;
			echo $layout_begin_col2.tep_draw_input_field('last_modified', $now, ' disabled ').$lb.$layout_end_col2;
			?>
	</div>
		
</div>
</div>

				
				
				
             <div class="modal-footer">
            <?php 
	/* <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button><?php echo tep_draw_button(MODULE_CONTENT_FOOTER_MODAL_CART_HEADING_TITLE . ($cart->count_contents() > 0 ? ' (' . $cart->count_contents() . ')' : ''), 'cart', tep_href_link('shopping_cart.php')) . tep_draw_button(IMAGE_BUTTON_CHECKOUT, 'fas fa-angle-right', tep_href_link('checkout_shipping.php', '', 'SSL')); */ 
				 echo tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('categories_manager.php', 'cPath=' . $cPath));
				 ?>
				 </form>
             </div>
            </div><!-- /.modal-body -->
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>   <!-- /.modal show-->
</div> <!-- /.catman-newproduct-modal-->