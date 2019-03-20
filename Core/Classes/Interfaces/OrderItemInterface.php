<?php
namespace Core\Classes\Interfaces;

interface OrderItemInterface {
    function get_image($data);

    function get_title($data);

    function get_ingradients($data);

    function get_price($data);

    function get_quantity($data);
}