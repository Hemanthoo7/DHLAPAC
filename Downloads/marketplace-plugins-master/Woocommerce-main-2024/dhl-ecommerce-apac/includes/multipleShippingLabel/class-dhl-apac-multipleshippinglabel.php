<?php
/**
 * DHL_APAC_MultipleShippingLabel setup
 *
 * @package Hyperlink Infosystem DHL Ecommerce APAC
 * @since   1.0
 */


/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {exit;}

/**
 * Hyperlink Infosystem DHL Ecommerce APAC MultipleShippinglable Class.
 *
 * @class DHL_APAC_MultipleShippingLabel
 */

if (!class_exists('DHL_APAC_MultipleShippingLabel')) {

    class DHL_APAC_MultipleShippingLabel
    {

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC MultipleShippinglable constructor.
         */
        public function __construct()
        {

            add_filter('bulk_actions-edit-shop_order', array($this, 'him_dhl_downloads_bulk_actions_edit_product'), 999);
            add_filter('handle_bulk_actions-edit-shop_order', array($this, 'him_dhl_shipping_bulk_lable_action_edit_shop_order'), 10, 3);
            add_filter('manage_edit-shop_order_columns', array($this, 'dhl_him_add_order_label_column_header'), 30);
            add_action('manage_shop_order_posts_custom_column', array($this, 'dhl_him_add_order_label_column_content'));

        }

        // Adding to admin order list bulk dropdown a custom action 'him_dhl_multipleShippinglable'
        public function him_dhl_downloads_bulk_actions_edit_product($actions)
        {

            $actions['him_dhl_multipleShippinglable']       = __('DHLeCS Shipping Bulk Label', 'dhl-ecommerce-apac');
            $actions['him_dhl_multipleShippinglableprint']  = __('DHLeCS Shipping Bulk Label Print', 'dhl-ecommerce-apac');
            return $actions;

        }

        // Make the action from selected orders
        public function him_dhl_shipping_bulk_lable_action_edit_shop_order($redirect_to, $action, $order_ids)
        {

            if ($action == 'him_dhl_multipleShippinglableprint') {

                $him_dhl_processed_bulk_lable_ids = array();

                foreach ($order_ids as $post_id) {

                    $dhl_him_multiple_order_id = wc_get_order($post_id);

                    $him_dhl_processed_bulk_lable_ids[] = $post_id;
                }
                
                $him_dhl_downloadlable_orderID = implode(',', $him_dhl_processed_bulk_lable_ids);
                return $redirect_to = add_query_arg(array(
                    'him_dhl_multipleShippinglabledownload' => '1',
                    'him_dhl_downloadlable_orderID' => $him_dhl_downloadlable_orderID,
                ), $redirect_to);

            } else {

                if ($action !== 'him_dhl_multipleShippinglable') {
                    return $redirect_to; // Exit
                }

                $him_dhl_processed_bulk_lable_ids = array();
                $him_dhl_generated_bulk_lable_ids = array();

                foreach ($order_ids as $post_id) {

                    $dhl_him_multiple_order_id = wc_get_order($post_id);

                    $him_dhl_processed_bulk_lable_ids[] = $post_id;
                }

                if (!empty($him_dhl_processed_bulk_lable_ids) && is_array($him_dhl_processed_bulk_lable_ids)) {

                    foreach ($him_dhl_processed_bulk_lable_ids as $dhlhimorderID) {

                        $him_dhl_order_lable_form_data_save = get_post_meta($dhlhimorderID, 'him_dhl_order_lable_form_data_save', true);

                        if ($him_dhl_order_lable_form_data_save !== 'yes') {

                            $dhl_apac_create_multipleorder_bulk_lable = dhl_apac_create_multipleorder_bulk_lable($dhlhimorderID);

                            _e($dhl_apac_create_multipleorder_bulk_lable);

                        } else {

                            $him_dhl_generated_bulk_lable_ids[] = $dhlhimorderID;
                        }

                    }

                }
                $him_dhl_impload_generated_lable = implode(',', $him_dhl_generated_bulk_lable_ids);
                return $redirect_to = add_query_arg(array(
                    'him_dhl_multipleShippinglable' => '1',
                    'update_all' => $action,
                    'him_dhl_generated_bulk_lable' => $him_dhl_impload_generated_lable,
                ), $redirect_to);

            }
            return $redirect_to;

        }

        public function dhl_him_add_order_label_column_header($columns)
        {

            $dhl_him_new_columns = array();

            foreach ($columns as $column_name => $column_info) {
                $dhl_him_new_columns[$column_name] = $column_info;

                if ('order_total' === $column_name) {
                    $dhl_him_new_columns['dhl_product_weigth']  = __('DHLeCS Weight', 'dhl-ecommerce-apac');
                    $dhl_him_new_columns['dhl_label_created']   = __('DHLeCS Shipping Label', 'dhl-ecommerce-apac');
                    $dhl_him_new_columns['dhl_tracking_number'] = __('DHLeCS Tracking', 'dhl-ecommerce-apac');
                }
            }

            return $dhl_him_new_columns;

        }

        public function dhl_him_add_order_label_column_content($column)
        {

            global $post;

            $getallweight         = array();
            $dhlhimimplodeweigth  = array();

            $dhl_him_multiple_order_id = $post->ID;

            if ($dhl_him_multiple_order_id) {

                if ('dhl_product_weigth' === $column) {

                    $dhl_him_orderget_items = wc_get_order($dhl_him_multiple_order_id);
                    $dhl_him_items          = $dhl_him_orderget_items->get_items();

                    if (!empty($dhl_him_items)) {
                        foreach ($dhl_him_items as $dhl_him_itemvalues) {
                            $dhlhimproduct_id   = $dhl_him_itemvalues->get_product_id();
                            $product_id_weight  = get_post_meta($dhlhimproduct_id, '_weight', true);
                            $getallweight[$dhl_him_multiple_order_id] = $product_id_weight;
                        }
                    }

                    if (!empty($getallweight)) {
                        foreach ($getallweight as $getallweightkey => $getallweightvalue) {
                            if (empty($getallweightvalue)) {
                                echo '<span style="color: red;">' . esc_html__( 'N/A', 'dhl-ecommerce-apac' ) . '</span>';
                            } else {
                                echo '<span style="color: green;">' . esc_html__( 'Y/A', 'dhl-ecommerce-apac' ) . '</span>';
                            }
                        }
                    }

                }

                if ('dhl_label_created' === $column) {

                    $him_dhl_order_lable_form_data_save = get_post_meta($dhl_him_multiple_order_id, 'him_dhl_order_lable_form_data_save', true);
                    $him_dhl_shipping_lable_mps_content = get_post_meta($dhl_him_multiple_order_id, 'him_dhl_shipping_lable_mps_content', true);

                    if (!empty($him_dhl_shipping_lable_mps_content)) {
                        if ($him_dhl_order_lable_form_data_save == 'yes') {
                            echo esc_html__( 'Y/A', 'dhl-ecommerce-apac' );
                        } else {
                            echo esc_html__( 'N/A', 'dhl-ecommerce-apac' );
                        }
                    } else {
                        echo esc_html__( 'N/A', 'dhl-ecommerce-apac' );
                    }

                }

                if ('dhl_tracking_number' === $column) {

                    $him_dhl_order_lable_form_data_save = get_post_meta($dhl_him_multiple_order_id, 'him_dhl_order_lable_form_data_save', true);
                    $him_dhl_order_shipment_id = get_post_meta($dhl_him_multiple_order_id, 'him_dhl_order_shipment_id', true);

                    if (!empty($him_dhl_order_shipment_id)) {
                        if ($him_dhl_order_lable_form_data_save == 'yes') {
                            if ($him_dhl_order_shipment_id) {
                                echo '<a href="' . esc_url( 'https://ecommerceportal.dhl.com/track/?ref=' . $him_dhl_order_shipment_id ) . '" target="'.esc_attr('_blank').'">' . esc_html( $him_dhl_order_shipment_id ) . '</a><br/>';
                            }
                        } else {
                            echo esc_html__( 'N/A', 'dhl-ecommerce-apac' );
                        }
                    } else {
                        echo esc_html__( 'N/A', 'dhl-ecommerce-apac' );
                    }

                }
            }
        }

    }
    new DHL_APAC_MultipleShippingLabel();
}