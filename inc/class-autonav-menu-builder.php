<?php

/**
 * Set up the menu logic for the autnav widget
 * TODO: Rework the class to use a factory to decide the styles of the different layouts
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Autonav_Menu_Builder {
    private $full_menu = array();
    private $final_menu = array();
	private $current_page = 0;
	private $current_page_path = array();

    /**
     * @param string $type
     */
    public function __construct($type = 'default') {
	    $this->current_page = get_the_ID();
        $menu_items = $this->autonav_setup_menu();
        $this->full_menu = $output = $this->autonav_multi_menu($menu_items, 'menu_item_parent', 'ID', 0);
    }

	public function build_navigation() {

	}

    public function autonav_traverse_menu() {

    }

    /**
     * Builds flat menu
     *
     * @return array containing all of our menu items
     */
    public function autonav_flat() {
        return $this->autonav_setup_menu();
    }

    /**
     * Builds the default menu
     *
     * @return array containing all of our menu items
     */
    public function autonav_default() {
        return $this->full_menu;
    }

    /**
     * Builds top level only menu
     *
     * @return array containing all of our menu items
     */
    public function autonav_top_level() {
        $menu = $this->autonav_setup_menu();

        // Pull out child menu items

        return $menu;
    }

    /**
     * Builds the title for autonav widget
     *
     * @return string containing the widget title
     */
    public function autonav_title() {
        $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

        return $title;
    }

	/**
	 * Sorts Wordpress menu items into multi-dimensional array
	 *
	 * @param $array
	 * @param $parent_key
	 * @param $item_key
	 * @param $current_id
	 * @param $current_page_path_ids The path of the current page in ids. Should not be passed or it will disrupt the system
	 * @return array A multi-dimensional array of items
	 */
	private function autonav_multi_menu(&$array, $parent_key, $item_key, $current_id, $current_page_path_ids = array()) {
		if(empty($array))
			return;
		$i = 0;
		$output = array();
		// Set current page to passed path ids
		$current_page_path = $current_page_path_ids;
		foreach($array as $key => $item) {
			// Is it a child of the passed object?
			if($item->$parent_key == $current_id) {
				// Add to the path
				$current_page_path[] = $i;

				// Add to output array
				$output[$i]['item'] = $item;
				// Add children to array
				$output[$i]['children'] = $this->autonav_multi_menu(
					$array,
					$parent_key,
					$item_key,
					$item->$item_key,
					$current_page_path
				);

				// If we are on the current page, set the object id
				if($item->object_id == $this->current_page)
					$this->current_page_path = $current_page_path;
				//unset($array[$key]);
				$i++;
			}
			// Reset the current page path
			$current_page_path = $current_page_path_ids;
		}
		return $output;
	}

	/**
	 * Gets all the menu items from Wordpress
	 * @return mixed
	 */
	private function autonav_setup_menu() {
		$menu_name = 'autonav-widget';
		$locations = get_nav_menu_locations();
		$menu = wp_get_nav_menu_items($locations[$menu_name]);

		return $menu;
	}

}
