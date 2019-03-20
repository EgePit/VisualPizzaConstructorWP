<?php
namespace Classes;

use Core\Classes\Abstracts\OrderItemMapper;

class OrderItem extends OrderItemMapper {
    /**
     * @param $data WC_Order_Item
     * @return void
     * */
    function get_image($data) {
        $this->image = get_the_post_thumbnail_url($data->get_product_id(), 'full');
    }

    /**
     * @param $data WC_Order_Item
     * @return void
     * */
    function get_title($data) {
        $this->title = $data->get_name();
    }

    /**
     * @param $data WC_Order_Item
     * @return void
     * */
    function get_ingradients($data) {
        if($ingradients = get_post_meta($data->get_product_id(), 'ingradients', true)) {
            foreach($ingradients as $ingradient_id) {
                $this->ingradients[] = get_post($ingradient_id)->post_title;
            }
        }
    }

    /**
     * @param $data WC_Order_Item
     * @return void
     * */
    function get_price($data) {
        if(is_array(get_post_meta($data->get_product_id(), 'ingradients', true)))
            $this->price = $this->pizzeria->get_product_price(get_post_meta($data->get_product_id(), 'ingradients', true));
        else
            $this->price = $this->pizzeria->get_side_product_price($data->get_product_id());
    }

    /**
     * @param $data WC_Order_Item
     * @return void
     * */
    function get_quantity($data) {
        $this->quantity = $data->get_quantity();
    }
}