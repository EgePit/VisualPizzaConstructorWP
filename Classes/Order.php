<?php
namespace Classes;

use Core\Classes\Abstracts\OrderMapper;

class Order extends OrderMapper {
    /**
     * @param $order WP_Post
     * @return integer
     * */
    function get_id($order) {
        $this->id = $order->ID;
    }

    /**
     * @param $order WP_Post
     * @return string
     * */
    function get_title($order) {
        $this->title = $order->post_title;
    }

    /**
     * @param $order WP_Post
     * @return string
     * */
    function get_date($order) {
        $this->date = $order->post_date;
    }

    /**
     * @param $order WP_Post
     * @return string
     * */
    function get_delivery($order) {
        $this->delivery = get_post_meta($order->ID, '_billing_delivery', true);
    }

    /**
     * @param $order WP_Post
     * @return string
     * */
    function get_when($order) {
        $this->when = get_post_meta($order->ID, '_billing_when', true);
    }

    /**
     * @param $order WP_Post
     * @return string
     * */
    function get_total_price($order) {
        $this->total_price = get_post_meta($order->ID, '_order_total', true);
    }

    /**
     * @param $order WP_Post
     * @return string
     * */
    function get_offer($order) {
        $this->offer = get_post_meta($this->id, 'offer', true);
    }

    function send_offer() {
        $body = '';
//        wp_mail()
    }
}