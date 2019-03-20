<?php
namespace Core\Classes\Abstracts;

use Core\Classes\Interfaces\ControlInterface;

use Core\Classes\VisualConstructor;
use Classes\MenuBuilder;
use Classes\Intolerance;
use Classes\Pizzeria;
use Core\Classes\AdminSettings;
use Classes\Order;
use Classes\OrderItem;

abstract class ControlMapper implements ControlInterface
{
    /**
     * Core class
     * @var $core VisualConstructor
     */
    public $core;

    /**
     * Addition plugins required
     * @var array
     * */
    var $dependencies = array(
        'woocommerce',
        'ultimate-member'
    );

    /**
     * @var $main_menu MenuBuilder
     */
    var $main_menu;

    /**
     * @var $side_menu MenuBuilder
     */
    var $side_menu;

    /**
     * @param $main_menu array
     * @param $side_menu array
     * @param $intolerance array
     * @param $upload_dir string
     * @param $pizzeria mixed
     * */
    public function init_vconst($main_menu, $side_menu, $intolerance, $upload_dir,  $pizzeria=null) {
        $this->main_menu = new MenuBuilder($main_menu);
        $this->side_menu = new MenuBuilder($side_menu);

        if(!is_null($pizzeria))
            $pizzeria = $this->get_pizzeria($pizzeria);

        $mode = 0;
        foreach($intolerance as $item) {
            $intolerance_list[] = new Intolerance($item);
        }

        $this->core = new VisualConstructor(
            $upload_dir,
            PLUGIN_URL,
            site_url().'/wp-json/vconst/v1',
            $mode,
            $this->main_menu,
            CHECKOUT_PAGE,
            $intolerance_list,
            $pizzeria
        );
    }

    /**
     * @param mixed
     * @return Pizzeria
     * */
    function get_pizzeria($pizzeria) {
        return new Pizzeria($pizzeria, $this->main_menu, $this->side_menu, get_available_gateways());
    }

    /**
     * @param $pizzerias array
     * @return array
     * */
    function pizzerias_list($pizzerias) {
        $pizzerias_list = array();
        foreach($pizzerias as $pizzeria) {
            $pizzerias_list[$pizzeria->post_name] = $this->get_pizzeria($pizzeria);
        }
        return $pizzerias_list;
    }

    /**
     * @param $product array
     * @return array
     * */
    function filter_pizzerias($product) {
        $pizzerias = $this->pizzerias_list($this->get_pizzerias());

        $a = array_filter($pizzerias, function($pizzeria) use ($product) {
            return filter_pizzeiras($pizzeria, $product);
        });

        return $a;
    }

    /**
     * Get pizzerias endpoint
     * */
    function get_pizzerias_list() {
        $product = (array)json_decode(file_get_contents('php://input'), true)['product'];
        $a = $this->filter_pizzerias($product);
        echo json_encode($a);
        exit;
    }

    /**
     * Get pizzeria orders array
     * @return array
     * */
    function get_pizzeria_orders() {
        $orders = array();
        $pizzeria = $this->get_pizzeria($this->get_pizzeria_page());

        foreach($pizzeria->orders as $order_id=> $status) {
            $order = $this->get_order_page($order_id);
            $orders[] = $this->get_pizzeria_order($order, $this->get_order_items($order_id), $status);
        }
        return $orders;
    }

    /**
     * Get order object
     * @param $order mixed
     * @param $order_items array
     * @param $status string
     * @param $pizzeria_id int
     * @return Order
     * */
    public function get_pizzeria_order($order, $order_items, $status, $pizzeria_id=null) {
        $main_products = array();
        $side_products = array();

        if(is_null($pizzeria_id))
            $pizzeria = $this->get_pizzeria($this->get_pizzeria_page());
        else
            $pizzeria = $this->get_pizzeria($this->get_pizzeria_page($pizzeria_id));

        foreach($order_items as $item) {
            $product_pattern = ucfirst(str_replace('-', ' ', ASSEMBLED_PRODUCT_SLUG));
            if(preg_match("/$product_pattern/", $item->get_name()))
                $main_products[] = new OrderItem($item, $pizzeria);
            else
                $side_products[] = new OrderItem($item, $pizzeria);
        }

        return new Order($order, $main_products, $side_products, $status, $pizzeria->id);
    }

    /**
     * Get order object
     * @return string
     * */
    function get_pizzeria_orders_html() {
        $html = '';
        $orders = $this->get_pizzeria_orders();
        foreach($orders as $order) {
            $html .= $order->get_order_html();
        }

        return $html;
    }

    /**
     * Pizzeria settings page
     * */
    function get_pizzeria_settings() {
        $pizzeria = $this->get_pizzeria($this->get_pizzeria_page());
        echo $pizzeria->get_settings_html();
    }

    /**
     * Super admin config page
     * @param $saved_styles array
     * @return string
     * */
    function get_superadmin_settings_page($saved_styles) {
        $admin_settings = new AdminSettings();
        return $admin_settings->get_admin_settings_html($saved_styles);
    }

    /**
     * Save Super admin config page
     * @return void
     * */
    function save_admin_settings() {
        $admin_settings = new AdminSettings();
        $admin_settings->save_custom_style($_POST);
    }

    /**
     * Get constructor data endpoint
     * */
    function get_constructor_data() {
        echo json_encode($this->core->get_constructor_data());
        exit;
    }

    /**
     * Get constructor view
     * @return void
     * */
    function get_constructor_page() {
        echo $this->core->get_constructor_html();
    }
}