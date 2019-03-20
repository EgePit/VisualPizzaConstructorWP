<?php
namespace Core\Classes\Abstracts;

use Core\Classes\Interfaces\OrderInterface;

abstract class OrderMapper implements OrderInterface {
    var $id = null;
    var $title = '';
    var $date = '';
    var $delivery = '';
    var $when = '';
    var $status = '';
    var $main_products = array();
    var $side_products = array();
    var $pizzeria_id = null;
    var $total_price = 0;
    var $offer = array();

    function __construct($data, $main, $side, $status, $pizzeria_id) {
        $this->get_id($data);
        $this->get_title($data);
        $this->get_date($data);
        $this->get_delivery($data);
        $this->status = $status;
        $this->get_when($data);
        $this->pizzeria_id = $pizzeria_id;
        $this->main_products = $main;
        $this->side_products = $side;
        $this->get_total_price($data);
    }

    function get_order_html() {
        ob_start();
        include(dirname(__FILE__) . '/../../view/order.php');
        $html = ob_get_contents();
        ob_clean();
        return $html;
    }
}