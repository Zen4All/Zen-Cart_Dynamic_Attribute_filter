<?php
/**
 * default_filter.php  for index filters
 *
 * index filter for the default product type
 * show the products of a specified manufacturer
 *
 * @package productTypes
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @todo Need to add/fine-tune ability to override or insert entry-points on a per-product-type basis
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: DrByte  Mon Oct 19 09:51:56 2015 -0400 Modified in v1.5.5 $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
if (isset($_GET['sort']) && strlen($_GET['sort']) > 3) {
  $_GET['sort'] = substr($_GET['sort'], 0, 3);
}
if (isset($_GET['alpha_filter_id']) && (int)$_GET['alpha_filter_id'] > 0) {
  $alpha_sort = " and pd.products_name LIKE '" . chr((int)$_GET['alpha_filter_id']) . "%' ";
} else {
  $alpha_sort = '';
}

// bof dynamic filter 1 of 2
include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_DYNAMIC_FILTER));
// eof dynamic filter 1 of 2

  if (!isset($select_column_list)) $select_column_list = "";
   // show the products of a specified manufacturer
  if (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] != '' ) {

// bof dynamic filter 2 of 2
  if (isset($_GET['filter_id']) && zen_not_null($_GET['filter_id']) || isset($_GET[DYNAMIC_FILTER_PREFIX . $categoryGroup])) {
// We are asked to show only a specific category
    $listing_sql = "SELECT DISTINCT " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, m.manufacturers_name, p.products_price, p.products_tax_class_id, pd.products_description,
                    IF(s.status = 1, s.specials_new_products_price, NULL) AS specials_new_products_price,
                    IF(s.status = 1, s.specials_new_products_price, p.products_price) AS final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
                    FROM " . TABLE_PRODUCTS . " p
                    LEFT JOIN " . TABLE_SPECIALS . " s ON p.products_id = s.products_id
                    LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id
                    LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id
                    JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p.products_id = p2c.products_id
                    " . ($filter_attr == true ? "
                      JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " p2a ON p.products_id = p2a.products_id
                      JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON p2a.options_id = po.products_options_id
                      JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON p2a.options_values_id = pov.products_options_values_id
                      " . (defined('TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK') ? "
                        JOIN " . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK . " p2as ON p.products_id = p2as.products_id " : "") : '') . "
                    WHERE p.products_status = 1
                    AND m.manufacturers_id = " . (int)$_GET['manufacturers_id'] . "
                    AND pd.language_id = " . (int)$_SESSION['languages_id'] .
                    $filter . "
                    GROUP BY p.products_id, s.status, s.specials_new_products_price
                    " . $having .
                    $alpha_sort;
  } else {
// We show them all
    $listing_sql = "SELECT DISTINCT " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description,
                    IF(s.status = 1, s.specials_new_products_price, NULL) AS specials_new_products_price,
                    IF(s.status = 1, s.specials_new_products_price, p.products_price) AS final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status 
                    FROM " . TABLE_PRODUCTS . " p
                    LEFT JOIN " . TABLE_SPECIALS . " s ON p.products_id = s.products_id
                    LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id
                    LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id" .
                    ($filter_attr == true ? "
                      JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " p2a ON p.products_id = p2a.products_id
                      JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON p2a.options_id = po.products_options_id
                      JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON p2a.options_values_id = pov.products_options_values_id" .
                    (defined('TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK') ? "
                      JOIN " . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK . " p2as ON p.products_id = p2as.products_id " : "") : '') . "
                    WHERE p.products_status = 1
                    AND pd.language_id = " . (int)$_SESSION['languages_id'] . "
                    AND m.manufacturers_id = " . (int)$_GET['manufacturers_id'] .
                    $filter . "
                    GROUP BY p.products_id, s.status, s.specials_new_products_price " .
                    $having .
                    $alpha_sort;
  }
} else {
// show the products in a given category
  if (isset($_GET['filter_id']) && zen_not_null($_GET['filter_id']) || isset($_GET[DYNAMIC_FILTER_PREFIX . $manufacturerGroup])) {
// We are asked to show only specific category
    $listing_sql = "SELECT DISTINCT " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description,
                    IF(s.status = 1, s.specials_new_products_price, NULL) AS specials_new_products_price,
                    IF(s.status = 1, s.specials_new_products_price, p.products_price) AS final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
                    FROM " . TABLE_PRODUCTS . " p
                    LEFT JOIN " . TABLE_SPECIALS . " s on p.products_id = s.products_id
                    LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id
                    LEFT JOIN " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id
                    JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c on p.products_id = p2c.products_id" .
                    ($filter_attr == true ? "
                      JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " p2a on p.products_id = p2a.products_id
                      JOIN " . TABLE_PRODUCTS_OPTIONS . " po on p2a.options_id = po.products_options_id
                      JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov on p2a.options_values_id = pov.products_options_values_id" .
                      (defined('TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK') ? "
                        JOIN " . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK . " p2as on p.products_id = p2as.products_id " : "") : '') . "
                    WHERE p.products_status = 1
                    AND pd.language_id = " . (int)$_SESSION['languages_id'] . "
                    AND p2c.categories_id = " . (int)$current_category_id .
                    $filter . "
                    GROUP BY p.products_id, s.status, s.specials_new_products_price " .
                    $having .
                    $alpha_sort;
  } else {
// We show them all
    $listing_sql = "SELECT DISTINCT " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description,
                    IF(s.status = 1, s.specials_new_products_price, NULL) AS specials_new_products_price,
                    IF(s.status = 1, s.specials_new_products_price, p.products_price) AS final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
                    FROM " . TABLE_PRODUCTS . " p
                    LEFT JOIN " . TABLE_SPECIALS . " s on p.products_id = s.products_id
                    LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id
                    JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c on p.products_id = p2c.products_id" .
                    ($filter_attr == true ? "
                      JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " p2a on p.products_id = p2a.products_id
                      JOIN " . TABLE_PRODUCTS_OPTIONS . " po on p2a.options_id = po.products_options_id
                      JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov on p2a.options_values_id = pov.products_options_values_id" .
                      (defined('TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK') ? "
                        JOIN " . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK . " p2as on p.products_id = p2as.products_id " : "") : '') . "
                    WHERE p.products_status = 1
                    AND pd.language_id = " . (int)$_SESSION['languages_id'] . "
                    AND p2c.categories_id = " . (int)$current_category_id .
                    $filter . "
                    GROUP BY p.products_id, s.status, s.specials_new_products_price
                    " . $having .
                    $alpha_sort;
  }
}
// eof dynamic filter 2 of 2

// set the default sort order setting from the Admin when not defined by customer
  if (!isset($_GET['sort']) and PRODUCT_LISTING_DEFAULT_SORT_ORDER != '') {
    $_GET['sort'] = PRODUCT_LISTING_DEFAULT_SORT_ORDER;
  }

  if (isset($column_list)) {
    if ((!isset($_GET['sort'])) || (isset($_GET['sort']) && !preg_match('/[1-8][ad]/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > sizeof($column_list)) ) {
      for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
        if (isset($column_list[$i]) && $column_list[$i] == 'PRODUCT_LIST_NAME') {
          $_GET['sort'] = $i+1 . 'a';
          $listing_sql .= " order by p.products_sort_order, pd.products_name";
          break;
        } else {
// sort by products_sort_order when PRODUCT_LISTING_DEFAULT_SORT_ORDER is left blank
// for reverse, descending order use:
//       $listing_sql .= " order by p.products_sort_order desc, pd.products_name";
          $listing_sql .= " order by p.products_sort_order, pd.products_name";
          break;
        }
      }
// if set to nothing use products_sort_order and PRODUCTS_LIST_NAME is off
      if (PRODUCT_LISTING_DEFAULT_SORT_ORDER == '') {
        $_GET['sort'] = '20a';
      }
    } else {
      $sort_col = substr($_GET['sort'], 0 , 1);
      $sort_order = substr($_GET['sort'], -1);
      switch ($column_list[$sort_col-1]) {
        case 'PRODUCT_LIST_MODEL':
          $listing_sql .= " order by p.products_model " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_NAME':
          $listing_sql .= " order by pd.products_name " . ($sort_order == 'd' ? 'desc' : '');
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $listing_sql .= " order by m.manufacturers_name " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $listing_sql .= " order by p.products_quantity " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_IMAGE':
          $listing_sql .= " order by pd.products_name";
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $listing_sql .= " order by p.products_weight " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_PRICE':
          $listing_sql .= " order by p.products_price_sorter " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
      }
    }
  }
// optional Product List Filter
  if (PRODUCT_LIST_FILTER > 0) {
    if (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] != '') {
      $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name
      from " . TABLE_PRODUCTS . " p, " .
      TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .
      TABLE_CATEGORIES . " c, " .
      TABLE_CATEGORIES_DESCRIPTION . " cd
      where p.products_status = 1
        and p.products_id = p2c.products_id
        and p2c.categories_id = c.categories_id
        and p2c.categories_id = cd.categories_id
        and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
        and p.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'
      order by cd.categories_name";
    } else {
      $filterlist_sql= "select distinct m.manufacturers_id as id, m.manufacturers_name as name
      from " . TABLE_PRODUCTS . " p, " .
      TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .
      TABLE_MANUFACTURERS . " m
      where p.products_status = 1
        and p.manufacturers_id = m.manufacturers_id
        and p.products_id = p2c.products_id
        and p2c.categories_id = '" . (int)$current_category_id . "'
      order by m.manufacturers_name";
    }
    $do_filter_list = false;
    $filterlist = $db->Execute($filterlist_sql);
    if ($filterlist->RecordCount() > 1) {
        $do_filter_list = true;
      if (isset($_GET['manufacturers_id'])) {
        $getoption_set =  true;
        $get_option_variable = 'manufacturers_id';
        $options = array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES));
      } else {
        $options = array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS));
      }
      while (!$filterlist->EOF) {
        $options[] = array('id' => $filterlist->fields['id'], 'text' => $filterlist->fields['name']);
        $filterlist->MoveNext();
      }
    }
  }