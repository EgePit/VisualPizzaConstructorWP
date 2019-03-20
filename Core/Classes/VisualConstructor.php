<?php
/*
 *Visual_constructor core class
 */
namespace Core\Classes;

class VisualConstructor {
    var $public_path;
    var $plugin_url;
    var $checkout_page;
    var $api_url;
    var $mode;
    var $menu = array();
    var $intolerance = array();
    var $pizzeria;
    var $fixedPizzeria = false;
    var $intolerance_list = array();
    var $currency = '';

    function __construct($public_path, $plugin_url, $api_url, $mode, $menu, $checkout_page, $intolerance, $pizzeria=null) {
        $this->public_path = $public_path;
        $this->plugin_url = $plugin_url;
        $this->api_url = $api_url;
        $this->mode = $mode;
        $this->menu = $menu->formated_menu;
        $this->pizzeria = $pizzeria;
        $this->checkout_page = $checkout_page;
        $this->intolerance_list = $intolerance;
        $this->currency = vconst_get_currency();
        if(!is_null($pizzeria))
            $this->fixedPizzeria = true;
    }

    function get_constructor_data() {
        $data = array(
            'menu' => $this->menu,
            'mode' => $this->mode,
            'checkout_page' => $this->checkout_page,
            'pizzeriaFixed' => $this->fixedPizzeria,
            'intolerance_list' => $this->intolerance_list,
            'currency' => $this->currency
        );

        if(!is_null($this->pizzeria))
            $data['pizzeria'] = $this->pizzeria;

        return $data;
    }

    function create_order_img($img_base64) {
        list($type, $img_base64) = explode(';', $img_base64);
        list(, $img_base64)      = explode(',', $img_base64);
        $img_data = base64_decode($img_base64);
        $img_title = rand(1, 9999999).'-'.time().'.png';
        file_put_contents($this->public_path.'/orders_img/'.$img_title, $img_data);
        return $img_title;
    }

    function get_constructor_html() {
        ob_start();
        include_once(dirname(__FILE__).'/../view/constructor.php');
        $html = ob_get_contents();
        ob_clean();
        return $html;
    }
}
