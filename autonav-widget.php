<?php
/**
 *  Plugin Name: Autonav Widget
 *  Description: This widget automatically populates a menu based on the page you are currently on.
 *  Version: 0.2.0
 *  Author: Sebo Marketing
 *  Author URI: http://sebomarketing.com
 *  Text Domain: autonav-widget
 *  License: GPLv3
 *  GitHub Plugin URI: https://github.com/robskidmore/autonav-widget
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sets up the autnav widget
 */

class Autonav_Widget extends WP_Widget {

    protected $widget_slug = 'autonav-widget';

    /**
     * Autonav widget constructor
     */

    public function __construct() {

        add_action('init', array($this, 'widget_textdomain'));

        parent::__construct(
            $this->get_widget_slug(),
            __('Autonav Widget', $this->get_widget_slug()),
            array(
                'classname' => $this->get_widget_slug().'-class',
                'description'   => __('This widget automatically populates a menu based on the page you are currently on', $this->get_widget_slug())
            )
        );

        $this->include_files();

        register_nav_menu('autonav-widget', __('Autonav Widget', $this->get_widget_slug()));

    }

    public function get_widget_slug() {
        return $this->widget_slug;
    }

    public function widget($args, $instance) {

        extract($args, EXTR_SKIP);
        $title = $instance['title'] ? $instance['title'] : 'Navigation';
        $menu_id = $instance['custom_menu'] ? $instance['custom_menu'] : 'default';
        $autonav_menu = new Autonav_Menu('default',$title, $menu_id);
        $autonav_template = new Autonav_Template;

        $menu = $autonav_menu->autonav_default();
        //$title = 'Title';
        echo $autonav_template->autonav_structure($menu, $menu['title']);

    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['custom_menu'] = $new_instance['custom_menu'];
        return $instance;
    }

    public function form($instance) {
        $instance = wp_parse_args((array) $instance, array('title' => '', 'custom_menu' => 'default'));
        $title = $instance['title'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Default Title', 'autonav-widget'); ?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
        <?php
        $menus = get_terms('nav_menu');
        $custom_menu = $instance['custom_menu'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('custom_menu'); ?>"><?php echo __('Custom Menu', 'autonav-widget'); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name('custom_menu'); ?>" id="<?php echo $this->get_field_id('custom_menu'); ?>">
                <option value="default"<?php echo $custom_menu === 'default' ? ' selected="selected"': ''; ?>>Default</option>
                <?php foreach($menus as $menu): ?>
                    <option value="<?php echo $menu->term_id; ?>"<?php echo $custom_menu === $menu->term_id ? ' selected="selected"': ''; ?>><?php echo $menu->name; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    private function include_files() {
        require_once( 'inc/class-autonav-menu.php' );
        require_once('inc/class-autonav-template.php');
    }

    public function widget_textdomain() {
        load_plugin_textdomain($this->get_widget_slug(), false, plugin_dir_path(__FILE__) . 'lang/');
    }
}

add_action('widgets_init', create_function('', 'return register_widget("Autonav_Widget");'));
