<?php

use Tygh\Registry;
use Tygh\Languages\Languages;

function fn_get_collection_data($collection_id = 0, $lang_code = CART_LANGUAGE)
{
    $collection = [];
    if (!empty ($collection_id)) {
        list($collections) = fn_get_collections ([
            'collection_id' => $collection_id
         ], 1 , $lang_code);
         $collection =!empty($collections) ? reset($collections) : [];
        if (!empty($collections)) {
            $collection = reset($collections);
            $collection['product_ids'] =  fn_collection_get_links($collection['collection_id']);

        }
    }
    return $collection;
}

function fn_get_collections($params = [], $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    // Set default values to input params
    $default_params = array(
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    if (AREA == 'C') {
        $params['status'] = 'A';
    }

    $sortings = array(
        'position' => '?:collections.position',
        'timestamp' => '?:collections.timestamp',
        'name' => '?:collection_descriptions.collection',
        'status' => '?:collections.status',
    );

    $condition = $limit = $join = '';

    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }

    $sorting = db_sort($params, $sortings, 'name', 'asc');

  
    if (!empty($params['item_ids'])) {
        $condition .= db_quote(' AND ?:collections.collection_id IN (?n)', explode(',', $params['item_ids']));
    }

    if (!empty($params['collection_id'])) {
        $condition .= db_quote(' AND ?:collections.collection_id = ?i', $params['collection_id']);
    }

    if (!empty($params['user_id'])) {
        $condition .= db_quote(' AND ?:collections.user_id = ?i', $params['user_id']);
    }

    if (!empty($params['status'])) {
        $condition .= db_quote(' AND ?:collections.status = ?s', $params['status']);
    }

    $fields = array (
        '?:collections.*',
        '?:collection_descriptions.collection',
        '?:collection_descriptions.description',
    );

    $join .= db_quote(' LEFT JOIN ?:collection_descriptions ON ?:collection_descriptions.collection_id = ?:collections.collection_id AND ?:collection_descriptions.lang_code = ?s', $lang_code);

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:collections $join WHERE 1 $condition");
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $collections = db_get_hash_array(
        "SELECT ?p FROM ?:collections " .
        $join .
        "WHERE 1 ?p ?p ?p",
        'collection_id', implode(', ', $fields), $condition, $sorting, $limit
    );

    $collection_image_ids = array_keys($collections);
    $images = fn_get_image_pairs($collection_image_ids, 'collection', 'M', true, false, $lang_code);

    foreach ($collections as $collection_id => $collection) {
        $collections[$collection_id]['main_pair'] = !empty($images[$collection_id]) ? reset($images[$collection_id]) : array();
    } 
    return array($collections, $params); 
}

function fn_update_collection($data, $collection_id, $lang_code = DESCR_SL)
{
    if (isset($data['timestamp'])) {
        $data['timestamp'] = fn_parse_date($data['timestamp']);
    }

    if (!empty($collection_id)) {
        db_query("UPDATE ?:collections SET ?u WHERE collection_id = ?i", $data, $collection_id);
        db_query("UPDATE ?:collection_descriptions SET ?u WHERE collection_id = ?i AND lang_code = ?s", $data, $collection_id, $lang_code);

    } else {
        $collection_id = $data['collection_id'] = db_replace_into('collections', $data);

        foreach (Languages::getAll() as $data['lang_code'] => $v) {
            db_query("REPLACE INTO ?:collection_descriptions ?e", $data);
        }
    }
    if (!empty($collection_id)) {
    fn_attach_image_pairs('collection', 'collection', $collection_id, $lang_code);

}
    $product_ids = !empty($data['product_ids']) ? $data ['product_ids'] : [];
    fn_collection_delete_links($collection_id);
    fn_collection_add_links($collection_id, $product_ids);
    
    return $collection_id;
}

function fn_delete_collection($collection_id)
{
    if (!empty($collection_id)) {
        $res = db_query("DELETE FROM ?:collections WHERE collection_id = ?i", $collection_id);
        db_query("DELETE FROM ?:collection_descriptions WHERE collection_id = ?i", $collection_id);
        fn_collection_delete_links($collection_id);
    }
}
function fn_collection_delete_links($collection_id)
{
    db_query("DELETE FROM ?:collection_links WHERE collection_id = ?i", $collection_id);

}
    function fn_collection_add_links($collection_id, $product_ids)
{
    if (!empty($product_ids)) {
        foreach ($product_ids as $product_id) {
            db_query("REPLACE INTO ?:collection_links ?e", [
                'product_id' => $product_id,
                'collection_id' => $collection_id,
            ]);
        }

    }
}
function fn_collection_get_links($collection_id)
{
    return !empty($collection_id) ? db_get_fields("SELECT product_id FROM ?:collection_links WHERE  collection_id = ?i", $collection_id) : [];
}