<?php
namespace Core\Classes\Interfaces;

interface MenuInterface {
    function get_group($item);

    function get_id($item);

    function get_title($item);

    function get_image($item);

    function get_thumb($item);

    function get_price($item);

    function get_default($item);

    function get_group_layer($item);

    function get_group_thumb($item);

    function get_group_rules($item);

    function get_intolerance($item);

    function get_last_modified_date($item);
}