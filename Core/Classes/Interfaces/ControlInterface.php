<?php
namespace Core\Classes\Interfaces;

interface ControlInterface {
    function init_vconst($main_menu, $side_menu, $upload_dir, $pizzeria);

    function get_pizzeria($pizzeria);

    function get_pizzerias_list();

    function get_pizzerias();

    function filter_pizzerias($product);

    function get_pizzeria_from_cart();

    function create_pizzeria($user_id);

    function get_pizzeria_orders();

    function get_order_data($order_id);

    function get_order_items($order_id);

    function get_pizzeria_order($order, $order_items, $status);

    function get_pizzeria_orders_html();

    function get_pizzeria_settings();

    function get_superadmin_settings_page($saved_styles);

    function get_constructor_data();

    function get_constructor_page();

    function done_order();

    function get_order_page($order_id);

    function get_pizzeria_page($pizzeria_id=null);
}