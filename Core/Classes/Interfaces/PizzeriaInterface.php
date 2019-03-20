<?php
namespace Core\Classes\Interfaces;

interface PizzeriaInterface {
    function get_id($data);

    function get_owner($data);

    function get_title($data);

    function get_description($data);

    function get_image($data);

    function get_settings ($data);

    function get_orders($data);

    function get_address($data);

    function get_product_price($data);
}