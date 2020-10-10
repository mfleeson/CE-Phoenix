<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

/* Products Links - Allow linking of different products together and display
   (C) Mark Fleeson. mark@burninglight.co.uk 2020 
   v 0.1
*/
require( 'includes/application_top.php' );
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// check all database tables are in place

// calculate link path
 $cPath = $_GET['cPath'] ?? '';
if ( tep_not_null( $cPath ) ) {
    $cPath_array = tep_parse_link_path( $cPath );
    $cPath = implode( '_', $cPath_array );
    $current_link_id = end( $cPath_array );
} else {
    $current_link_id = 0;
}


//Process Actions 

$action = ( isset( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : '' );

if ( tep_not_null( $action ) ) {
    switch ( $action ) {
		
		case 'delete':

            $active_changed = 0;

            if ( isset( $_GET[ 'prodID' ] ) ) {
                tep_db_query( "DELETE FROM products_links WHERE products_id='" . tep_db_input( $_GET[ 'prodID' ] ) . "' and linked_products_id='". tep_db_input( $_GET[ 'linkID' ] ) . "' ");
				
                
                $active_changed += tep_db_affected_rows();
            }


            $messageStack->add_session( sprintf( 'link Deleted ' . $_GET[ 'prodID' ], $active_changed ), 'success' );

            tep_redirect( tep_href_link( 'products_links.php' ) );

            break;
        case 'swop':

            $active_changed = 0;

            if ( isset( $_GET[ 'prodID' ] ) ) {
                $update_query = tep_db_query( "UPDATE products_links SET active = NOT active WHERE products_id='" . tep_db_input( $_GET[ 'prodID' ] ) .  "' and linked_products_id='". tep_db_input( $_GET[ 'linkID' ] ) ."'" );
                $active_changed += tep_db_affected_rows();
            }


            $messageStack->add_session( sprintf( 'Active Status changed for link ' . $_GET[ 'prodID' ], $active_changed ), 'success' );

            tep_redirect( tep_href_link( 'products_links.php' ) );

            break;
        case 'insert_link':
      
           if ( isset( $_POST[ 'product_id1' ] ) )
			{$product_id1 = tep_db_prepare_input( $_POST[ 'product_id1' ] ); } else { $product_id1=0; };
		   if ( isset( $_POST[ 'product_id2' ] ) )
			{$product_id2 = tep_db_prepare_input( $_POST[ 'product_id2' ] ); } else { $product_id2=0; };
			
		   if ( isset( $_POST[ 'linkboth' ] ) )
			{$linkboth = tep_db_prepare_input( $_POST[ 'linkboth' ] ); } else { $linkboth=true; };
			
			
            

           if($product_id1 >0 && $product_id2 > 0) {

            if ( $action == 'insert_link' ) {
				if($linkboth == true) {
					$res = tep_db_query("REPLACE INTO products_links VALUES (".$product_id1.",".$product_id2.");");
					$res = tep_db_query("REPLACE INTO products_links VALUES (".$product_id2.",".$product_id1.");");
					
				}
				else
				{
					
					$res = tep_db_query("REPLACE INTO products_links VALUES (".$product_id1.",".$product_id2.");");
				}
			}
		   }
    } 
};

//Start Main Form
require( 'includes/template_top.php' );




$product_links_query_sql = "SELECT pl.*, pd.products_name,pd2.products_name AS linked_product FROM products_links pl LEFT JOIN products_description pd ON ( pd.products_id = pl.products_id)
LEFT JOIN products_description pd2 ON (pd2.products_id = pl.linked_products_id) WHERE pd2.products_id = pl.linked_products_id order by products_id,linked_products_id asc";

$product_links_query = tep_db_query( $product_links_query_sql );

while ( $link = tep_db_fetch_array( $product_links_query ) ) {
    $link_work[  ] = $link;
}




//require( 'includes/admin_module_templates/products_links_new.php' );
//require('includes/admin_module_templates/link_manager_settings.php');
?>
<div class="row">
  <div class="col-12 ">
    <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
	  <p><?php echo HEADING_DESCRIPTION; ?></p>
  </div>

</div>
 

<div class="row">
<!--  <div class="col-4 col-sm-2"> <a class="btn btn-danger btn-block btn-sm xxx text-white"><span onclick="$('#newProduct').modal('show'); " title="New link"><span class="fas fa-cog" aria-hidden="true"></span> New</span></a> </div> -->
  
</div>
 <form name="new_link" <?php echo 'action="' . tep_href_link('products_links.php', tep_get_all_get_params(['action', 'info', 'sID']) . '&action=' .'insert_link'). '"'; ?> method="post">
  <div class="form-group row">
	  <div class="col-md-3">
      <label for="linkProduct1" class="col-form-label text-left text-sm-right"><?php echo TEXT_NEW_PRODUCT_LINK_ID1; ?></label>
      <?php  echo tep_draw_input_field('product_id1',  '', 'id="product_id1" ');  ?>     
	  </div>
	
	  <div class="col-md-3">
      <label for="linkProduct2" class="col-form-label text-left text-sm-right"><?php echo TEXT_NEW_PRODUCT_LINK_ID2; ?></label>
     <?php  echo tep_draw_input_field('product_id2',  '', ' ');  ?>     
       
	  </div>
	<div class="col-md-3">
     
      <label for="single" class="col-form-label  text-left text-sm-right"><?php echo TEXT_NEW_PRODUCT_BIDIRECTIONAL; ?></label>
      
        <?php 
		  echo   tep_draw_selection_field('linkboth', 'checkbox', true, true,' class="form-control" id="linkboth"');
         ?>    
      </div>
	     
       
	<div class="col-md-3">
		 <?php
    echo $OSCOM_Hooks->call('product_links', 'formNew');
    
    echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg');
		 
    ?>
		
    </div>
    
	 </div>
   
    
   

  </form>
<div class="row no-gutters">
  <div class="col">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="thead-dark">
          <tr>
            <th><?php echo TABLE_HEADING_PRODUCT_ID; ?></th>
            <th><?php echo TABLE_HEADING_NAME; ?></th>
            <th><?php echo TABLE_HEADING_LINKED_PRODUCT_ID; ?></th>
            <th><?php echo TABLE_HEADING_LINKED_NAME; ?></th>
            <th><?php echo TABLE_HEADING_ACTIVE; ?></th>
            <th><?php echo TABLE_HEADING_ACTION; ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
			$linkmaster = $link_work;

          foreach ( $linkmaster as $link ) {

              $pid = $link[ 'products_id' ];
			  $lpid = $link[ 'linked_products_id' ];

              $prodname = tep_output_string_protected( $link[ 'products_name' ] );
			  $lprodname = tep_output_string_protected( $link[ 'linked_product' ] );
              $active = $link[ 'active' ];
              echo '<tr>';
              echo '<td>';
              echo $pid . '</td>';
              echo '<td>' . $prodname . '</td>';
              echo '<td>' . $lpid . '</td>';
              echo '<td>' . $lprodname . '</td>';
              echo '<td onclick="document.location.href=\'' . tep_href_link( 'products_links.php', tep_get_all_get_params( array( 'prodID','linkID', 'action' ) ) . 'action=swop&prodID=' . ( int )$pid .'&linkID=' . ( int )$lpid ).'\'">';

              if ( $active == 1 ) {
                  echo '<i class="fas fa-check-circle text-success"></i>';
              } else {
                  echo '<i class="fas fa-times-circle text-danger"></i>';
              };
              echo '</td>';
              echo '<td>';
              
              echo '<a   href="' . tep_href_link( 'products_links.php', tep_get_all_get_params( array( 'prodID','linkID', 'action' ) ) . 'action=delete&' . 'prodID=' . ( int )$pid  .'&linkID=' . ( int )$lpid ).' " alt="Delete ' . $prodname . ' " > ';
              echo '<i class="fas fa-trash-alt "></i></a>';
              echo '</td>';
              echo '</tr>';


          }


          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>




<?php

require( 'includes/template_bottom.php' );
require( 'includes/application_bottom.php' );
?>
