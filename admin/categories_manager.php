<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

/* Categories Manager - Suite to allow disabling categories and general management
   (C) Mark Fleeson. www.burninglight.co.uk 2020 
   v 0.1
*/

require( 'includes/application_top.php' );

function checkinstall() {


    $catman_table_exists = true;
    $catman_data_in_sync = true;
    $output = '';

    $checksql = "SELECT COUNT(*) as co FROM information_schema.tables WHERE 
table_name = 'categories_management' and table_schema='" . DB_DATABASE . "';";
    $checkquery = tep_db_query( $checksql );
    $obj = tep_db_fetch_array( $checkquery );
    if ( $obj[ 'co' ] == 0 ) {
        $catman_table_exists = false;
    }
    if ( $catman_table_exists == true ) {
        $cat_row_count_query = tep_db_query( "SELECT COUNT(*) as catco FROM categories;" );
        $cat_row_obj = tep_db_fetch_array( $cat_row_count_query );
        $cat_row_count = $cat_row_obj[ 'catco' ];
        $catman_row_count_query = tep_db_query( "SELECT COUNT(*) as catmanco FROM categories_management;" );
        $catman_row_obj = tep_db_fetch_array( $catman_row_count_query );
        $catman_row_count = $catman_row_obj[ 'catmanco' ];
        if ( $cat_row_count <> $catman_row_count ) {
            $catman_data_in_sync = false;
        }

    } else {
        $catman_data_in_sync = false;

    };

    if ( $catman_table_exists == false ) {


        tep_db_query( "CREATE TABLE `categories_management` (
  `categories_id` int(11) NOT NULL DEFAULT '0',
  `active` int(1) NOT NULL DEFAULT '0',
  `categories_heading_title` varchar(255) DEFAULT '',
  `products_sort` enum('Alphabetical','SortOrder','NewestDesc','OldestDesc') DEFAULT 'Alphabetical',
  `categories_hide_from_groups` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`categories_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8" );
        $output .= 'Categories_management table created.<br>';
    }
    if ( $catman_data_in_sync == false ) {
        tep_db_query( "INSERT INTO categories_management SELECT categories_id, 1,'','Alphabetical',0 FROM categories WHERE
categories_id NOT IN (SELECT categories_id FROM categories_management);" );
        $output .= 'Existing categories synced with categories_management.<br>';


        return $output;
    }


}

function SortHierarchy( & $category_work ) {
    $parent = 1;
    while ( $parent > 0 ) {
        foreach ( $category_work as & $category ) {
            $parent = $category[ 'parent_id' ];
            if ( $parent > 0 ) {
                $category[ 'categories_name' ] = $category_work[ $parent ][ 'categories_name' ] . ">>" . $category[ 'categories_name' ];
                $category[ 'parent_id' ] = $category_work[ $parent ][ 'parent_id' ];
            }
        }
    }
}

// check all database tables are in place
$installcheck = '';
$installcheck = checkinstall();

// calculate category path
// $cPath = $_GET['cPath'] ?? '';
if ( tep_not_null( $cPath ) ) {
    $cPath_array = tep_parse_category_path( $cPath );
    $cPath = implode( '_', $cPath_array );
    $current_category_id = end( $cPath_array );
} else {
    $current_category_id = 0;
}

//Get extra category_management fields struture
$categories_management_structure_query = tep_db_query( "show columns from categories_management" );

while ( $catman = tep_db_fetch_array( $categories_management_structure_query ) ) {
    if ( $catman[ 'Field' ] <> 'categories_id' ) {
        $catmanfields[] = $catman[ 'Field' ];
    }
}
//Process Actions 

$action = ( isset( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : '' );

if ( tep_not_null( $action ) ) {
    switch ( $action ) {
        case 'swop':

            $active_changed = 0;

            if ( isset( $_GET[ 'catID' ] ) ) {
                $update_query = tep_db_query( "UPDATE categories_management SET active = NOT active WHERE categories_id='" . tep_db_input( $_GET[ 'catID' ] ) . "'" );
                $active_changed += tep_db_affected_rows();
            }


            $messageStack->add_session( sprintf( 'Active Status changed for Category ' . $_GET[ 'catID' ], $active_changed ), 'success' );

            tep_redirect( tep_href_link( 'categories_manager.php' ) );

            break;
        case 'insert_category':
        case 'update_category':

            if ( isset( $_GET[ 'categories_id' ] ) )$categories_id = tep_db_prepare_input( $_GET[ 'categories_id' ] );

            if ( isset( $_POST[ 'no_parent' ] ) ) {
                $no_parent = 0;
            } else {
                $no_parent = -1;
            }

            if ( isset( $_POST[ 'parent' ] ) )$parent = tep_db_prepare_input( $_POST[ 'parent' ] );
            if ( $no_parent == 0 )$parent = 0;

            $active_val = 'off';
            if ( isset( $_POST[ 'product_active' ] ) )$active_val = tep_db_prepare_input( $_POST[ 'active' ] );
            if ( $active_val == 'on' ) {
                $active = 1;
            } else {
                $active = 0;
            }

            if ( isset( $_POST[ 'product_sort' ] ) )$product_sort = tep_db_prepare_input( $_POST[ 'product_sort' ] );

            $sort_order = tep_db_prepare_input( $_POST[ 'sort_order' ] );
            $outp[ 'cat_id' ] = $categories_id;
            $outp[ 'no_parent' ] = $no_parent;
            $outp[ 'parent' ] = $parent;
            $outp[ 'active' ] = $active;
            $outp[ 'prodsort' ] = $product_sort;

            $sql_data_array = array( 'sort_order' => ( int )$sort_order );

            foreach ( $catmanfields as $catman ) {
                switch ( $catman ) {
                    case 'active':
                        $catman_sql_data_array[ $catman ] = ( int )$active;
                        break;

                    case 'products_sort':
                        $catman_sql_data_array[ $catman ] = $product_sort;
                        break;
                    default:
                        $catman_sql_data_array[ $catman ] = tep_db_prepare_input( $_POST[ $catman ] );
                }
            }
            $outp[ 'catman' ] = $catman_sql_data_array;

            $outp[ 'action' ] = $action;

            if ( $action == 'insert_category' ) {
                $insert_sql_data = array( 'parent_id' => $parent,
                    'date_added' => 'now()',
                    'last_modified' => 'now()' );

                $sql_data_array = array_merge( $sql_data_array, $insert_sql_data );

                tep_db_perform( 'categories', $sql_data_array );

                $categories_id = tep_db_insert_id();
                $catman_sql_data_array[ 'categories_id' ] = ( int )$categories_id;

                tep_db_perform( 'categories_management', $catman_sql_data_array );

            } elseif ( $action == 'update_category' ) {
                $update_sql_data = array( 'last_modified' => 'now()' );

                $sql_data_array = array_merge( $sql_data_array, $update_sql_data );

                $outp[ 'categories_result' ] = tep_db_perform( 'categories', $sql_data_array, 'update', "categories_id = '" . ( int )$categories_id . "'" );
                $outp[ 'categories_management_result' ] = tep_db_perform( 'categories_management', $catman_sql_data_array, 'update', "categories_id = '" . ( int )$categories_id . "'" );
            }


            $languages = tep_get_languages();
            for ( $i = 0, $n = sizeof( $languages ); $i < $n; $i++ ) {
                $categories_name_array = $_POST[ 'categories_name' ];
                $categories_description_array = $_POST[ 'categories_description' ];
                $categories_seo_description_array = $_POST[ 'categories_seo_description' ];
                $categories_seo_keywords_array = $_POST[ 'categories_seo_keywords' ];
                $categories_seo_title_array = $_POST[ 'categories_seo_title' ];

                $language_id = $languages[ $i ][ 'id' ];

                $sql_data_array = array( 'categories_name' => tep_db_prepare_input( $categories_name_array[ $language_id ] ) );
                $sql_data_array[ 'categories_description' ] = tep_db_prepare_input( $categories_description_array[ $language_id ] );
                $sql_data_array[ 'categories_seo_description' ] = tep_db_prepare_input( $categories_seo_description_array[ $language_id ] );
                $sql_data_array[ 'categories_seo_keywords' ] = tep_db_prepare_input( $categories_seo_keywords_array[ $language_id ] );
                $sql_data_array[ 'categories_seo_title' ] = tep_db_prepare_input( $categories_seo_title_array[ $language_id ] );
                $outp[ 'langsql' ][ $language_id ] = $sql_data_array;

                if ( $action == 'insert_category' ) {
                    $insert_sql_data = array( 'categories_id' => $categories_id,
                        'language_id' => $languages[ $i ][ 'id' ] );

                    $sql_data_array = array_merge( $sql_data_array, $insert_sql_data );

                    $outp[ 'categories_description_result' ][ $language_id ] = tep_db_perform( 'categories_description', $sql_data_array );
                } elseif ( $action == 'update_category' ) {
                    $outp[ 'categories_description_result' ][ $language_id ] = tep_db_perform( 'categories_description', $sql_data_array, 'update', "categories_id = '" . ( int )$categories_id . "' and language_id = '" . ( int )$languages[ $i ][ 'id' ] . "'" );
                }
            }

            $categories_image = new upload( 'categories_image' );
            $categories_image->set_destination( DIR_FS_CATALOG_IMAGES );

            if ( $categories_image->parse() && $categories_image->save() ) {
                $outp[ 'image' ] = tep_db_query( "update categories set categories_image = '" . tep_db_input( $categories_image->filename ) . "' where categories_id = '" . ( int )$categories_id . "'" );
            }

            tep_redirect( tep_href_link( 'categories_manager.php', 'cPath=' . $cPath . '&cID=' . $categories_id ) );
            break;

    }
};

//Start Main Form
require( 'includes/template_top.php' );


//Build Categories Structure

$base_categories_query_sql = "SELECT c.categories_id, TRIM(cd.categories_name) AS categories_name, c.parent_id,
IF(ISNULL(c.sort_order),0,c.sort_order) AS sort_order,cm.active
FROM categories c, categories_description cd, categories_management cm
WHERE cd.categories_id = c.categories_id AND cm.categories_id = c.categories_id;";

$base_categories_query = tep_db_query( $base_categories_query_sql );

while ( $category = tep_db_fetch_array( $base_categories_query ) ) {
    $category_work[ $category[ 'categories_id' ] ] = $category;
}

$category_work_copy = $category_work;
SortHierarchy( $category_work );

$catname = array_column( $category_work, 'categories_name' );
$sortorder = array_column( $category_work, 'sort_order' );
array_multisort( $catname, SORT_ASC, $sortorder, SORT_ASC, $category_work );

foreach ( $category_work as & $category ) {

    $category[ 'parent_id' ] = $category_work_copy[ $category[ 'categories_id' ] ][ 'parent_id' ];

}


require( 'includes/admin_module_templates/category_manager_new.php' );
//require('includes/admin_module_templates/category_manager_settings.php');
?>
<div class="row">
  <div class="col-12 col-sm-6">
    <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
  </div>
  <?php

  echo '<div class="col-8 col-sm-4">';

  echo tep_draw_form( 'search', 'categories_manager.php', '', 'get' );
  echo tep_draw_input_field( 'search', null, 'placeholder="' . TEXT_FILTER_SEARCH . '" class="form-control form-control-sm mb-1"' );
  echo tep_draw_hidden_field( 'category' ) . tep_hide_session_id();
  echo '</form>';
  echo tep_draw_form( 'filter', 'categories_manager.php', '', 'get' );
  echo tep_draw_pull_down_menu( 'category', $categories_list_array, null, 'class="form-control form-control-sm" onchange="this.form.submit();"' );
  echo tep_draw_hidden_field( 'search' ) . tep_hide_session_id();
  echo '</form>';
  ?>
</div>
</div>
<?php
if ( $installcheck <> '' ) {
    echo '<div class="row>"><div class="col-12"><h4>' . $installcheck . '</h4></div></div>';
}
?>
<div class="row">
  <div class="col-4 col-sm-2"> <a class="btn btn-danger btn-block btn-sm xxx text-white"><span onclick="$('#newProduct').modal('show'); " title="New Category"><span class="fas fa-cog" aria-hidden="true"></span> New</span></a> </div>
  <?php
  /* <div class="col-4 col-sm-2">
	  <a class="btn btn-danger btn-block btn-sm xxx text-white"><span onclick="$('#settings').modal('show'); " title="Settings"><span class="fas fa-cog" aria-hidden="true"></span> Settings</span></a>
    </div>
	*/
  ?>
</div>
<div class="row no-gutters">
  <div class="col">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="thead-dark">
          <tr>
            <th><?php echo TABLE_HEADING_ID; ?></th>
            <th><?php echo TABLE_HEADING_NAME; ?></th>
            <th><?php echo TABLE_HEADING_PARENT; ?></th>
            <th><?php echo TABLE_HEADING_ORDER; ?></th>
            <th><?php echo TABLE_HEADING_ACTIVE; ?></th>
            <th><?php echo TABLE_HEADING_ACTION; ?></th>
          </tr>
        </thead>
        <tbody>
          <?php


          foreach ( $category_work as $category ) {

              $catid = $category[ 'categories_id' ];

              $catname = tep_output_string_protected( $category[ 'categories_name' ] );
              $parentid = $category[ 'parent_id' ];
              $sort_order = $category[ 'sort_order' ];
              $active = $category[ 'active' ];
              echo '<tr>';
              echo '<td  onclick="document.location.href=\'' . tep_href_link( 'categories_manager.php', tep_get_all_get_params( array( 'catID' ) ) . 'catID=' . ( int )$category[ 'categories_id' ] ) . '\'">';
              echo $catid . '</td>';
              echo '<td>' . $catname . '</td>';
              echo '<td>' . $parentid . '</td>';
              echo '<td>' . $sort_order . '</td>';
              echo '<td onclick="document.location.href=\'' . tep_href_link( 'categories_manager.php', tep_get_all_get_params( array( 'catID', 'action' ) ) . 'action=swop&' . 'catID=' . ( int )$category[ 'categories_id' ] ) . '\'">';

              if ( $active == 1 ) {
                  echo '<i class="fas fa-check-circle text-success"></i>';
              } else {
                  echo '<i class="fas fa-times-circle text-danger"></i>';
              };
              echo '</td>';
              echo '<td>';
              echo '<a  href="' . tep_href_link( 'categories_manager.php', tep_get_all_get_params( array( 'catID', 'action' ) ) . 'action=edit&' . 'catID=' . ( int )$category[ 'categories_id' ] ) . '" alt="Edit ' . $catname . ' ">';
              echo '<i class="fas fa-edit "></i></a>&nbsp;&nbsp;';
              echo '<a   href="' . tep_href_link( 'categories_manager.php', tep_get_all_get_params( array( 'catID', 'action' ) ) . 'action=delete&' . 'catID=' . ( int )$category[ 'categories_id' ] ) . '" alt="Delete ' . $catname . ' " > ';
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


if ( $action == 'edit' ) {
    ?>
<script>
	$(window).load(function() {

jQuery.noConflict();
   jQuery('#newProduct').modal('show');
});
</script>
<?php
}
?>
<?php
require( 'includes/template_bottom.php' );
require( 'includes/application_bottom.php' );
?>
