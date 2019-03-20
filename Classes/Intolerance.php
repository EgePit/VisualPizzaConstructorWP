<?php
namespace Classes;

use Core\Classes\Abstracts\IntoleranceMapper;

class Intolerance extends IntoleranceMapper {
    /**
     * @param $data WP_Term
     * @return integer
     * */
    function get_id($data) {
        return $data->term_id;
    }

    /**
     * @param $data WP_Term
     * @return string
     * */
    function get_title($data) {
        return $data->name;
    }
}