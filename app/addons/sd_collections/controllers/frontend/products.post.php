<?php
/***************************************************************************
*                                                                          *
*   (c) Simtech Development Ltd.                                           *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
***************************************************************************/

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied'); 

if ($mode == 'collections') {
  
  Tygh::$app['session']['continue_url'] = "products.collections";

  $params = $_REQUEST;

//   $params['user_id'] = Tygh::$app['session']['auth']['user_id'];


  if (Registry::get('settings.General.show_products_from_subcategories') == 'Y') {
      $params['subcats'] = 'Y';
  }

  list($collections, $search) = fn_get_collections($params, Registry::get('settings.Appearance.products_per_page'), CART_LANGUAGE);
  // fn_print_die($collections);

  if (isset($search['page']) && ($search['page'] > 1) && empty($collections)) {
      return array(CONTROLLER_STATUS_NO_PAGE);
  }

  Tygh::$app['view']->assign('collections', $collections);
  Tygh::$app['view']->assign('search', $search);
  Tygh::$app['view']->assign('columns', 4);

  fn_add_breadcrumb(__('sd_collections.collections'));


} elseif ($mode == 'collection') {

  $collection_data = [];
  $collection_id = !empty($_REQUEST['collection_id']) ? $_REQUEST['collection_id'] : 0;
  $collection_data = fn_get_collection_data ($collection_id, CART_LANGUAGE);

  fn_add_breadcrumb(__('sd_collections.collection'));

      if (empty($collection_data)) {
          return [CONTROLLER_STATUS_NO_PAGE];
      }


  Tygh::$app['view']->assign('collection_data', $collection_data);

  fn_add_breadcrumb($collection_data['collection']);


  $params = $_REQUEST;
  $params['extend'] = ['description'];

  //Перебор массива продуктов в список id продуктов.
  $params['item_ids'] = !empty($collection_data['product_ids']) ? implode(',', $collection_data['product_ids']) : -1;


  if ($items_per_page = fn_change_session_param(Tygh::$app['session']['search_params'], $_REQUEST, 'items_per_page')) {
      $params['items_per_page'] = $items_per_page;
  }
  if ($sort_by = fn_change_session_param(Tygh::$app['session']['search_params'], $_REQUEST, 'sort_by')) {
      $params['sort_by'] = $sort_by;
  }
  if ($sort_order = fn_change_session_param(Tygh::$app['session']['search_params'], $_REQUEST, 'sort_order')) {
      $params['sort_order'] = $sort_order;
  }


  list($products, $search) = fn_get_products($params, Registry::get('settings.Appearance.products_per_page'));

  fn_gather_additional_products_data($products, [
      'get_icon'      => true,
      'get_detailed'  => true,
      'get_options'   => true,
      'get_discounts' => true,
      'get_features'  => false
  ]);

  $selected_layout = fn_get_products_layout($_REQUEST);
  // fn_print_die($search);
  Tygh::$app['view']->assign('products', $products);
  Tygh::$app['view']->assign('search', $search);
  Tygh::$app['view']->assign('selected_layout', $selected_layout);
}

