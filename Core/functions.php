<?php
define('ORDER_ACCEPTED', 'Accepted');
define('ORDER_DENY', 'Deny');
define('ORDER_PENDING', 'Pending');
define('ORDER_OFFER', 'Offer');
define('ORDER_DELIVERED', 'Delivered');

function filter_pizzeiras($pizzeria, $product) {
    foreach($product as $group=> $items) {
        foreach($items as $item) {
            if(isset($item['id']))
                $item_id = $item['id'];
            else
                $item_id = $item;

            $key = array_search($item_id, array_column($pizzeria->main_menu[$group]['options'], 'id'));
            if(!is_int($key))
                return false;
        }
    }
    return true;
}

function remove_empty($a) {
    if(!is_null($a))
        return $a;
}

function check_product_latest_update($last_edit_ts, $product_update) {
    if($last_edit_ts < $product_update) return $product_update;
    else return $last_edit_ts;
}

function prepare_data_for_cart($params, $main_menu, $side_menu, $pizzeria) {
    $last_edit_ts = 0;
    $core_menu = $main_menu->formated_menu;
    uksort($params['product'], function($a, $b) use($core_menu) {
        if ((int)$core_menu[$a]['layer'] == (int)$core_menu[$b]['layer']) {
            return 0;
        }
        return ((int)$core_menu[$a]['layer'] < (int)$core_menu[$b]['layer']) ? -1 : 1;
    });
    foreach($params['product'] as $group=> $group_items) {
        if(!is_multi($params['product'][$group])) {
            $params['product'][$group] = array();
            $params['product'][$group][] = $main_menu->search_menu_item_by_id($group_items['id'], $group);
            $params['last_edit_ts'] = check_product_latest_update($last_edit_ts, $params['product'][$group][0]['post_modified']);
            continue;
        }

        foreach($group_items as $key=> $menu_item) {
            $params['product'][$group][$key] = $main_menu->search_menu_item_by_id($menu_item['id'], $group);
            $params['last_edit_ts'] = check_product_latest_update($last_edit_ts, $params['product'][$group][$key]['post_modified']);
        }
    }
    foreach($params['side_order'] as $key=> $menu_item) {
        $params['side_order'][$key] = $side_menu->search_menu_item_by_id($menu_item['id'], 'side-menu');
        $params['side_order'][$key]['amount'] = $menu_item['amount'];
    }

    $ingradients = array();
    $title = array();
    $price = array();
    foreach($params['product'] as $group_name=> $group) {
        $group = array_filter($group, 'remove_empty');
        if(empty($group)) continue;
        $title[] = array_map(
            function($a) {
                if(isset($a['title']))
                    return $a['title'];
            }, $group);

        $ingradients[] = array_map(
            function($a) {
                if(isset($a['id']))
                    return $a['id'];
            }, $group);
        $price[] = array_map(
            function($a) use ($pizzeria, $group_name) {
                foreach($pizzeria->main_menu[$group_name]['options'] as $item) {
                    if(isset($item['price']) && $item['id'] == $a['id'])
                        return $item['price'];
                }

                return $a['price'];
            }, $group);
    }
    $params['ingradients'] = array_reduce($ingradients, 'array_merge', array());
    $title = implode(', ', array_reduce($title, 'array_merge', array()));
    $params['title'] = ucfirst(str_replace('-', ' ', ASSEMBLED_PRODUCT_SLUG)).': '.$title;
    $params['price_val'] = array_sum(array_reduce($price, 'array_merge', array()));
    return $params;
}

function is_multi($a) {
    $rv = array_filter($a,'is_array');
    if(count($rv)>0) return true;
    return false;
}
