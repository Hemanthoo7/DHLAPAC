<?php
/**
 * Hyperlink Infosystem Ecommerce APAC Pickup custom post types
 *
 * @package Hyperlink Infosystem DHL Ecommerce APAC
 * @since   1.0
 */

/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {exit;}

/**
 * Main Hyperlink Infosystem DHL Ecommerce APAC Pickup Account custom post types class.
 *
 * @class DHL_APAC_Custom_Post_Types
 */
if (!class_exists('DHL_APAC_Custom_Post_Types')) {

    class DHL_APAC_Custom_Post_Types
    {

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC custom post types constructor.
         */
        public function __construct()
        {

            $custom_post_type_array = array(
                'him_pickup_account_custom_post_type',
            );

            foreach ($custom_post_type_array as $key => $value) {
                add_action('init', array($this, $value));
                flush_rewrite_rules();
            }
        }
        /**
         * Custom post type for pickup account posts
         */
        public function him_pickup_account_custom_post_type()
        {
            $labels = array(
                'name'                  => _x('Pickup Account Information', 'Post type general name', 'dhl-ecommerce-apac'),
                'singular_name'         => _x('Pickup Account Information', 'Post type singular name', 'dhl-ecommerce-apac'),
                'menu_name'             => _x('Manage Pickup Account', 'Admin Menu text', 'dhl-ecommerce-apac'),
                'name_admin_bar'        => _x('Pickup Account Information', 'Add New on Toolbar', 'dhl-ecommerce-apac'),
                'add_new'               => __('Add New', 'dhl-ecommerce-apac'),
                'add_new_item'          => __('Add Pickup Account Information', 'dhl-ecommerce-apac'),
                'new_item'              => __('New Pickup Account', 'dhl-ecommerce-apac'),
                'edit_item'             => __('Edit Pickup Account', 'dhl-ecommerce-apac'),
                'view_item'             => __('View pickup account', 'dhl-ecommerce-apac'),
                'all_items'             => __('Manage Pickup Account', 'dhl-ecommerce-apac'),
                'search_items'          => __('Search Pickup Account', 'dhl-ecommerce-apac'),
                'parent_item_colon'     => __('Parent Pickup Account:', 'dhl-ecommerce-apac'),
                'not_found'             => __('No Pickup Account Information Found.', 'dhl-ecommerce-apac'),
                'not_found_in_trash'    => __('No Pickup Account Found In Trash.', 'dhl-ecommerce-apac'),
            );
            $args = array(
                'labels'                => $labels,
                'description'           => 'Pickup Account Custom Post Type.',
                'menu_icon'             => 'dashicons-admin-multisite',
                'public'                => true,
                'publicly_queryable'    => false,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'query_var'             => true,
                'rewrite'               => array('slug' => 'him_pickup_account'),
                'capability_type'       => 'post',
                'has_archive'           => true,
                'hierarchical'          => true,
                'menu_position'         => 999,
                'has_archive'           => true,
                'show_in_rest'          => false,
                'supports'              => array('title'),

            );
            
            register_post_type('him_pickup_account', $args);
        }
    }
    new DHL_APAC_Custom_Post_Types();
}