<?php
namespace Core\Classes\Interfaces;

interface OrderInterface {
    function get_id($order);

    function get_title($order);

    function get_date($order);

    function get_delivery($order);

    function get_when($order);

//    function get_status($order);

//    function get_pizzeria($order);

    function get_total_price($order);

    function get_offer($order);

    function send_offer();
}