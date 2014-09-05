<?php
/*
Plugin Name: WP-SAMW
Plugin URI: http://www.stegasoft.de/
Description: WordPress Sidebar-Accordion-Menu-Widget for widgetable sidebars
Version: 0.1
Author: Stephan G&auml;rtner
Author URI: http://www.stegasoft.de
Min WP Version: 3.3
*/






//===== VAR / CONST =======================================================
$wp_samw = "0.1";

define('WPSAMW_URLPATH', plugins_url()."/".plugin_basename( dirname(__FILE__)));




//===== INITIALIZE WIDGET ======================================
function wp_samw_init() {
  register_widget('WP_samw_Widget');
}
add_action('widgets_init', 'wp_samw_init');



//===== CLEAR OPTIONS AFTER DISABLING PLUGIN ====================
register_deactivation_hook(__FILE__, 'wpsamw_deinstall');
function wpsamw_deinstall() {
  delete_option('widget_samw_menu');
}



//==== FRONTEND HEADER CODE ======================================

function wpsamw_head() {

  $jscript_includes = "\n\n<!-- SAMW START -->\n";
  $jscript_includes .= '<link rel="stylesheet" href="'.WPSAMW_URLPATH.'/samw.css.php" type="text/css" />'."\n";
  //$jscript_includes .= '<script src="'.WPSAMW_URLPATH.'/samw.js.php" type="text/javascript"></script>'."\n";
  $jscript_includes .= "<!-- SAMW END -->\n\n";
  echo $jscript_includes;

  wp_enqueue_script('samw_js', WPSAMW_URLPATH.'/samw.js.php',array('jquery'));

}
add_action('wp_head', 'wpsamw_head');



//===== WIDGET_CLASS =============================================
class WP_samw_Widget extends WP_Widget {
    function __construct() {
        // widget actual processes
                $samw_widget_ops = array( 'description' => __('Add a custom accordion menu to your sidebar.') );
                parent::__construct( 'samw_menu', __('SAMW Custom Accordion Menu'), $samw_widget_ops );
    }

    function form($instance) {
      // outputs the options form on admin
               $title = isset( $instance['title'] ) ? $instance['title'] : '';
               $samw_menu = isset( $instance['samw_menu'] ) ? $instance['samw_menu'] : '';
               //$samw_speed = isset( $instance['samw_speed'] ) ? $instance['samw_speed'] : '';

                // Get menus
                $menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );

                // If no menus exists, direct the user to go and create some.
                if ( !$menus ) {
                        echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
                        return;
                }

                ?>
                <p>
                        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
                        <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
                </p>
                <p>
                        <label for="<?php echo $this->get_field_id('samw_menu'); ?>"><?php _e('Select Menu:'); ?></label>
                        <select id="<?php echo $this->get_field_id('samw_menu'); ?>" name="<?php echo $this->get_field_name('samw_menu'); ?>">
                                <option value="0"><?php _e( '&mdash; Select &mdash;' ) ?></option>
                <?php
                        foreach ( $menus as $menu ) {
                                echo '<option value="' . $menu->term_id . '"'
                                        . selected( $samw_menu, $menu->term_id, false )
                                        . '>'. esc_html( $menu->name ) . '</option>';
                        }
                ?>
                        </select>

                </p>

                <?php /*
                <p>
                        <label for="<?php echo $this->get_field_id('samw_speed'); ?>"><?php _e('Speed:') ?></label>
                        <input type="text" class="widefat" id="<?php echo $this->get_field_id('samw_speed'); ?>" name="<?php echo $this->get_field_name('samw_speed'); ?>" value="<?php echo $samw_speed; ?>" />
                </p>

                */ ?>



    <?php

    }

    function update($new_instance, $old_instance) {
        // processes widget options to be saved
                $instance = array();
                if ( ! empty( $new_instance['title'] ) ) {
                        $instance['title'] = strip_tags( stripslashes($new_instance['title']) );
                }
                if ( ! empty( $new_instance['samw_menu'] ) ) {
                        $instance['samw_menu'] = (int) $new_instance['samw_menu'];
                }
                /*
                if ( ! empty( $new_instance['samw_speed'] ) ) {
                        $instance['samw_speed'] = (int) $new_instance['samw_speed'];
                }
                */
                return $instance;
    }

    function widget($args, $instance) {
        // outputs the content of the widget
                // Get menu
                $samw_menu = ! empty( $instance['samw_menu'] ) ? wp_get_nav_menu_object( $instance['samw_menu'] ) : false;

                if ( !$samw_menu )
                        return;

                /** This filter is documented in wp-includes/default-widgets.php */
                $instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

                echo $args['before_widget'];

                if ( !empty($instance['title']) )
                        echo $args['before_title'] . $instance['title'] . $args['after_title'];


                $samw_menu_id = $this->number;

                echo "\n\n<!-- SAMW ($samw_menu_id) START -->\n";


                //======= Configure Item-List for jQuery use ==========


                $nav_settings = array(
                                  'theme_location'  => '',
                                  'menu'            => $samw_menu,
                                  'container'       => 'div',
                                  'container_class' => '',
                                  'container_id'    => '',
                                  'menu_class'      => 'menu samw-menu',
                                  'menu_id'         => 'menu-sidebar-'.$samw_menu_id,
                                  'echo'            => false,
                                  'fallback_cb'     => '',
                                  'before'          => '',
                                  'after'           => '',
                                  'link_before'     => '',
                                  'link_after'      => '',
                                  'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                  'depth'           => 0,
                                  'walker'          => ''
                );

                $menu_code = wp_nav_menu($nav_settings);

                //$menu_code = str_replace('class="sub-menu"', 'class="sub-menu" id="sub-menu-'.$samw_menu_id.'", $menu_code);

                echo $menu_code;

                echo "\n<!-- SAMW ($samw_menu_id) END -->\n\n";


                echo $args['after_widget'];
    }

}








?>