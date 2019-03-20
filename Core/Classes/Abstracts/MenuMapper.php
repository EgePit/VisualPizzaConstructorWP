<?php
namespace Core\Classes\Abstracts;

use Core\Classes\Interfaces\MenuInterface;

abstract class MenuMapper implements MenuInterface {
    var $formated_menu;

    /**
     * @param $origin_menu mixed
     * @return void
     * */
    function __construct($origin_menu) {
        foreach($origin_menu as $origin_menu_item) {
            $this->formated_menu[$this->get_group($origin_menu_item)]['options'][] = array(
                'id' => $this->get_id($origin_menu_item),
                'title'=>$this->get_title($origin_menu_item),
                'img' => $this->get_image($origin_menu_item),
                'thumb' => $this->get_thumb($origin_menu_item),
                'price' => (float)$this->get_price($origin_menu_item),
                'default' => $this->get_default($origin_menu_item),
                'intolerance' => $this->get_intolerance($origin_menu_item),
                'post_modified' => $this->get_last_modified_date($origin_menu_item),
            );

            if(!isset($this->formated_menu[$this->get_group($origin_menu_item)]['layer']))
                $this->formated_menu[$this->get_group($origin_menu_item)]['layer'] = $this->get_group_layer($origin_menu_item);

            if(!isset($this->formated_menu[$this->get_group($origin_menu_item)]['thumb']))
                $this->formated_menu[$this->get_group($origin_menu_item)]['thumb'] = $this->get_group_thumb($origin_menu_item);

            if(!isset($this->formated_menu[$this->get_group($origin_menu_item)]['rules']))
                $this->formated_menu[$this->get_group($origin_menu_item)]['rules'] = $this->get_group_rules($origin_menu_item);
        }
    }

    /**
     * @param $id int
     * @param $group string
     * */
    function search_menu_item_by_id($id, $group) {
        $search['iter'] = 0;
        $search['id'] = $id;
        $search['key'] = null;
        array_walk_recursive($this->formated_menu[$group]['options'], function($item, $key) use (&$search){
            if($item == $search['id'] && $key === 'id') {
                $search['key'] = $search['iter'];
            }

            if($key === 'id') {
                $search['iter']++;
            }
        });
        return $this->formated_menu[$group]['options'][$search['key']];
    }
}