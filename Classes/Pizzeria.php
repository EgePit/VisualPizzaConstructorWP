<?php
namespace Classes;

use Core\Classes\Abstracts\PizzeriaMapper;

class Pizzeria extends PizzeriaMapper {
    /**
     * @param $data WP_Post
     * @return void
     * */
    function get_id($data) {
        $this->id = $data->ID;
    }

    /**
     * @param $data WP_Post
     * @return void
     * */
    function get_owner($data) {
        $this->owner = (int)get_post_meta($data->ID, 'owner', true);
    }

    /**
     * @param $data WP_Post
     * @return void
     * */
    function get_title($data) {
        $this->title = $data->post_title;
    }

    /**
     * @param $data WP_Post
     * @return void
     * */
    function get_description($data) {
        $this->description = $data->post_content;
    }

    /**
     * @param $data WP_Post
     * @return void
     * */
    function get_image($data) {
        $this->image = get_user_meta($this->owner, 'cover_photo', true) ? wp_upload_dir()['baseurl'].'/ultimatemember/'.$this->owner.'/'.get_user_meta($this->owner, 'cover_photo', true) : '';
    }

    /**
     * @param $data WP_Post
     * @return void
     * */
    function get_settings ($data) {
        $this->settings = get_post_meta($data->ID, 'constructor_settings', true);
    }

    /**
     * @param $data WP_Post
     * @return void
     * */
    function get_orders($data) {
        $this->orders = get_post_meta($data->ID, 'orders', true);
    }

    /**
     * @param $data WP_Post
     * @return void
     * */
    function get_address($data) {
        $owner = get_post_meta($data->ID, 'owner', true);
        $this->address = get_user_meta($owner, 'billing_address_1', true);
    }
}