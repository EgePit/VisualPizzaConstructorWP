<?php
namespace Classes;

use Core\Classes\Abstracts\MenuMapper;

class MenuBuilder extends MenuMapper {
    /**
     * @param $item WP_Post
     * @return string
     * */
    function get_group($item) {
        return wp_get_post_terms($this->get_id($item), 'product_cat')[0]->slug;
    }

    /**
     * @param $item WP_Post
     * @return integer
     * */
    function get_id($item) {
        return $item->ID;
    }

    /**
     * @param $item WP_Post
     * @return string
     * */
    function get_title($item) {
        return $item->post_title;
    }

    /**
     * @param $item WP_Post
     * @return string
     * */
    function get_image($item) {
        return get_the_post_thumbnail_url($this->get_id($item), 'full');
    }

    /**
     * @param $item WP_Post
     * @return string
     * */
    function get_thumb($item) {
        return get_the_post_thumbnail_url($this->get_id($item), 'thumbnail');
    }

    /**
     * @param $item WP_Post
     * @return string
     * */
    function get_price($item) {
        return get_post_meta($this->get_id($item), '_price', true);
    }

    /**
     * @param $item WP_Post
     * @return boolean
     * */
    function get_default($item) {
        return get_post_meta($this->get_id($item), 'default', true) ? true : false;
    }

    /**
     * @param $item WP_Post
     * @return string
     * */
    function get_group_layer($item) {
        $group = wp_get_post_terms($this->get_id($item), 'product_cat')[0];
        return get_term_meta($group->term_id, 'term_meta-layer', true);
    }

    /**
     * @param $item WP_Post
     * @return string
     * */
    function get_group_thumb($item) {
        $group = wp_get_post_terms($this->get_id($item), 'product_cat')[0];
        $thumb_id = get_woocommerce_term_meta( $group->term_id, 'thumbnail_id', true );
        return wp_get_attachment_image_src($thumb_id, 'thumbnail')[0];
    }

    /**
     * @param $item WP_Post
     * @return array
     * */
    function get_group_rules($item) {
        $rules = array();

        $group = wp_get_post_terms($this->get_id($item), 'product_cat')[0];
        if(!empty(get_term_meta($group->term_id, 'term_meta-required')))
            $rules['required'] = true;

        if(!empty(get_term_meta($group->term_id, 'term_meta-max-1')))
            $rules['max'] = 1;

        return $rules;
    }

    /**
     * @param $item WP_Post
     * @return array
     * */
    function get_intolerance($item) {
        $tags = array_map(function($tag) {
            return $tag->term_id;
        }, wp_get_post_terms($this->get_id($item), 'product_tag'));

        return $tags;
    }

    /**
     * @param $item WP_Post
     * @return string
     * */
    function get_last_modified_date($item) {
        return $item->post_modified;
    }
}