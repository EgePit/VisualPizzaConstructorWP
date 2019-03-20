<?php
namespace Core\Classes\Abstracts;

use Core\Classes\Interfaces\OrderItemInterface;

abstract class OrderItemMapper implements OrderItemInterface {
    var $title;
    var $price;
    var $image;
    var $quantity;
    var $ingradients = array();
    var $pizzeria;

    function __construct($data, $pizzeria) {
        $this->get_title($data);
        $this->get_image($data);
        $this->get_ingradients($data);
        $this->get_quantity($data);
        $this->pizzeria = $pizzeria;
        $this->get_price($data);
    }
}