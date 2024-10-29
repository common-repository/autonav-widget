<?php

/**
 * Set up the menu logic for the autnav widget
 * TODO: Rework the class to use a factory to decide the styles of the different layouts
 * TODO: Get classes that are input through the navigation in the admin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

class Autonav_Menu {
	private $full_menu = array();
	private $final_menu = array();
	private $current_page = 0;
	private $current_page_path = array();

	/**
	 * @param string $type
	 */
	public function __construct($type = 'default', $default_title = 'Navigation', $menu_id = 'default') {
		$this->current_page = [
            'id' => get_the_ID(),
            'path' => $_SERVER['REQUEST_URI'],
            'full_url' => get_home_url() . $_SERVER['REQUEST_URI']
        ];
		$this->default_title = $default_title;
		$menu_items = $this->autonav_setup_menu($menu_id);
		$this->full_menu = $output = $this->autonav_multi_menu($menu_items, 'menu_item_parent', 'ID', 0);
		$this->final_menu = $this->build_navigation();

	}

	public function build_navigation() {
		return $this->get_current_page($this->current_page_path, $this->full_menu);

	}

	/**
	 * Get current page
	 *
	 * TODO: Split the transverse functionality out into it's own function
	 * @param $item_path
	 * @param $menu_items
	 * @param null $parent
	 *
	 * @return mixed
	 */
	public function get_current_page( $item_path, $menu_items, $parent = null ) {
		$current_step = array_shift($item_path);
		if(count($item_path) < 1) {
			$item = $menu_items[$current_step];
			$item['parent'] = $parent;
			$item['title'] = $this->get_title($item);
			if($this->has_children($item))
			{
				$item['menu_items'] = $item['children'];
			}
			else
			{
				if($parent !== null)
					$item['menu_items'] = $parent['children'];
				else
					$item['menu_items'] = $menu_items;
			}
			return $item;
		} else {
			return $this->get_current_page($item_path, $menu_items[$current_step]['children'], $menu_items[$current_step]);
		}
	}

    /**
     * Does the item have any children
     *
     * @param $item
     * @return bool
     */
    public function has_children($item) {
		if(isset($item['children']) && !empty($item['children']))
			return true;
		else
			return false;
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
		return $this->final_menu;
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
	public function get_title($item) {
		if($this->has_children($item))
		{
			return $item['item']->title;
		} else {
			if($item['parent'] === null) {
				return $this->default_title;
			} else {
				return $item['parent']['item']->title;
			}

		}

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
				if($item->url === $this->current_page['path'] || $item->url === $this->current_page['full_url']) {
					$this->current_page_path = $current_page_path;
					$item->classes[] = 'current-menu-item';
				}

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
	private function autonav_setup_menu($menu_id) {
        if($menu_id === 'default') {
            $menu_name = 'autonav-widget';
            $locations = get_nav_menu_locations();
            $menu = wp_get_nav_menu_items($locations[$menu_name]);
        } else {
            $menu = wp_get_nav_menu_items($menu_id);
        }

		return $menu;
	}

}
