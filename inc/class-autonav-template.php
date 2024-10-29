<?php

/**
 * Set up the menu logic for the autnav widget
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Autonav_Template {


    /**
     * Builds the default menu includes all menu items
     *
     * @param $menu and $title
     * @return $autonav
     */
    public function autonav_structure($menu, $title) {

        $autonav = $before_widget;
        ob_start();

            $autonav .='<div class="autonav-widget-container">';

                //Widget Title
                $autonav .='<h3 class="widget-title">' . $title . '</h3>';

                if ($menu['menu_items']) {

                    //Menu Structure
                    $autonav .='<ul id="' . $widget_slug . '">';

                    foreach($menu['menu_items'] as $menu_item) {

                        $autonav .='<li class="' . implode(' ',$menu_item['item']->classes) . '"><a href="' . $menu_item['item']->url . '">' . $menu_item['item']->title . '</a></li>';
                    }

                    $autonav .='</ul>';
                } else {
                    $autonav .='Please configure the Autonav Widget by logging into your wordpress site and choosing a menu in Appearance -> Menus';
                }
                
            $autonav .='</div><!--end .autonav-widget-container-->';

        $autonav .= ob_get_clean();
        $autonav .= $after_widget;

        return $autonav;
    }
}
