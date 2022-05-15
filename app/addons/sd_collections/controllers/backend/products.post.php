<?php

use Tygh\Registry;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($mode == 'update_collection') {
        $collection_id = !empty($_REQUEST['collection_id']) ? $_REQUEST['collection_id'] : 0;
        $data = !empty($_REQUEST['collection_data']) ? $_REQUEST['collection_data'] : [];
        $collection_id = fn_update_collection($data, $collection_id);
        if (!empty($collection_id)) {
    
            $suffix = ".update_collection?collection_id={$collection_id}";
        } else  {
  
            $suffix = ".add_collection";
        }      
    


    } elseif ($mode == 'update_collections') {
        if (!empty($_REQUEST['collection_data'])) {
            foreach ($_REQUEST['collection_data'] as $collection_id => $data)  { 
            fn_update_collection($data,$collection_id);
        }  
    }    
        $suffix = ".manage_collections";


    } elseif ($mode == 'delete_collection') {
        // fn_print_die($_REQUEST);

        $collection_id =!empty($_REQUEST['collection_id']) ? $_REQUEST['collection_id'] : 0;
        fn_delete_collection($collection_id);
        $suffix = ".manage_collections";

    } 
         elseif ($mode == 'delete_collections') {
            // fn_print_die($_REQUEST);
        if (!empty($_REQUEST['collections_ids'])) {
        foreach ($_REQUEST['collections_ids'] as $collection_id){
            fn_delete_collection($collection_id);
            }
        }
        $suffix = ".manage_collections";
    }

    return [CONTROLLER_STATUS_OK, 'products' . $suffix];

}

    if ($mode == 'add_collection' || $mode == 'update_collection') { 
    $collection_id = !empty($_REQUEST['collection_id']) ? $_REQUEST['collection_id'] : 0;
    $collection_data = fn_get_collection_data ($collection_id, DESCR_SL);
    

    if (empty($collection_data) && $mode == 'update') {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
  
    Tygh::$app['view']->assign([
        'collection_data' => $collection_data,
        'u_info' => !empty ($collection_data['user_id']) ? fn_get_user_short_info($collection_data['user_id']) : [],
    ]);
    
 

} elseif ($mode=='manage_collections' ) {

    list($collections, $search) = fn_get_collections($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);


    Tygh::$app['view']->assign('collections', $collections);
    Tygh::$app['view']->assign('search', $search);
}