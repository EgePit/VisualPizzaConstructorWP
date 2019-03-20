<?php
namespace Core\Classes\Abstracts;

use Core\Classes\Interfaces\PizzeriaInterface;
use Core\Classes\GoogleRating;

abstract class PizzeriaMapper implements PizzeriaInterface {
    public $id;
    public $owner;
    public $title;
    public $description;
    public $rating;
    public $image;
    public $side_menu = array();
    public $full_side_menu = array();
    public $full_main_menu = array();
    public $main_menu = array();
    public $payments = array();
    public $settings = array();
    public $orders = array();
    public $address = '';
    public $place_id = '';

    function __construct($data, $main, $side, $payments) {
        $this->get_id($data);
        $this->get_owner($data);
        $this->get_title($data);
        $this->get_description($data);
        $this->get_image($data);
        $this->get_settings($data);
        $this->get_orders($data);
        $this->get_address($data);
        $google_rationg = new GoogleRating($this->address);
        $this->rating = $google_rationg->rating;
        $this->side_menu = $side->formated_menu['side-menu']['options'];
        $this->full_side_menu = $side->formated_menu['side-menu']['options'];
        $this->full_main_menu = $main->formated_menu;
        $this->main_menu = $main->formated_menu;
        $this->payments = $payments;

        $this->filter_side_menu();
        $this->filter_main_menu();
    }

    function filter_side_menu() {
        foreach($this->side_menu as $key=> $menu_item) {
            if(isset($this->settings['menu'][$menu_item['title']]))
                $this->side_menu[$key]['price'] = $this->settings['menu'][$menu_item['title']]['price'];
            if(isset($this->settings['menu'][$menu_item['title']]) && $this->settings['menu'][$menu_item['title']]['status'] == "false")
                unset($this->side_menu[$key]);
        }
    }

    function filter_main_menu() {
        foreach($this->main_menu as $group=> $menu_group) {
            foreach($menu_group['options'] as $key=> $menu_item) {
                if(isset($this->settings['menu'][$menu_item['title']]))
                    $this->main_menu[$group]['options'][$key]['price'] = $this->settings['menu'][$menu_item['title']]['price'];
                if(isset($this->settings['menu'][$menu_item['title']]) && $this->settings['menu'][$menu_item['title']]['status'] == "false")
                    unset($this->main_menu[$group]['options'][$key]);
            }
        }
    }

    function get_settings_html() {
        ob_start();
        include_once(dirname(__FILE__) . '/../../view/settings.php');
        $html = ob_get_contents();
        ob_clean();
        return $html;
    }

    function get_product_price($data) {
        $price = 0;
        foreach($this->main_menu as $group) {
            foreach($group['options'] as $item) {
                if(in_array($item['id'], $data))
                    $price += $item['price'];
            }
        }

        return (int) $price;
    }

    function get_side_product_price($data) {
        $price = 0;
        foreach($this->side_menu as $item) {
            if($item['id'] == $data) {
                $price = $item['price'];
            }
        }
        return (int) $price;
    }

    function get_pizzeria_order_status($order_id) {
        return $this->orders[$order_id];
    }
}