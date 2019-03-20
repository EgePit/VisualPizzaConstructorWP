<?php
namespace Core\Classes\Abstracts;

use Core\Classes\Interfaces\IntoleranceInterface;

abstract class IntoleranceMapper implements IntoleranceInterface {
    var $id;
    var $title;

    /**
     * @param $data mixed
     * @return void
     * */
    function __construct($data) {
        $this->id = $this->get_id($data);
        $this->title = $this->get_title($data);
    }
}