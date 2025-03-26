<?php
/**
 * DHL_APAC_CreateShippingLabel setup
 *
 * @package Hyperlink Infosystem DHL Ecommerce APAC
 * @since   1.0
 */


/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {exit;}

/**
 * Hyperlink Infosystem DHL Ecommerce APAC Shippinglable Class.
 *
 * @class DHL_APAC_CreateShippingLabel
 */

if (!class_exists('DHL_APAC_CreateShippingLabel')) {

    class DHL_APAC_CreateShippingLabel
    {

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC Shippinglable constructor.
         */
        public function __construct()
        {

            add_action('add_meta_boxes', array($this, 'him_order_shipping_lable_add_meta_box'), 20);
            add_action('woocommerce_process_shop_order_meta', array($this, 'him_order_shipping_lable_save_meta_box'), 10, 2);

        }

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC Add the meta box for shipment info on the order page
         *
         */
        public function him_order_shipping_lable_add_meta_box()
        {
            add_meta_box('him-woocommerce-shipment-dhl-label', sprintf(__('DHLeCS Shipping Label', 'dhl-ecommerce-apac'), 'dhl-ecommerce-apac'), array($this, 'him_shipping_lable_meta_box'), 'shop_order', 'normal', 'default');
        }

        /**
         * Show the meta box for shipment info on the order page
         *
         */
        public function him_shipping_lable_meta_box()
        {

            global $woocommerce, $post, $product;

            $current_user_id        = get_current_user_id();

            $dhl_pickup_account     = get_user_meta($current_user_id, 'dhl_pickup_account', true);
            $dhl_prefix             = get_user_meta($current_user_id, 'dhl_prefix', true);
            $dhl_order              = new WC_Order($post->ID);
            $dhl_order_id           = $dhl_order->get_id();
            //$dhl_order_id           = trim(str_replace('#', '', $dhl_order->get_order_number()));

            $getOrderproductmname   = wc_get_order($dhl_order_id);
            $getOrderproductdata    = $getOrderproductmname->get_data();
            if (!empty($getOrderproductmname)) {
                $productnamearray   = array();
                $weightmetaarray    = array();
                foreach ($getOrderproductmname->get_items() as $order_item) {
                    $productnamearray[] = $order_item->get_name();
                    $weightmetaarray[]  = $order_item->get_product_id();
                }
            }

            if (!empty($weightmetaarray)) {
                $getallweight               = array();
                $him_dhl_shipping_weight    = array();
                foreach ($weightmetaarray as $weight) {
                    if (!empty($weight)) {
                        $getallweight[] = get_post_meta($weight, '_weight', true);
                    }
                }
                $weight_unit = get_option('woocommerce_weight_unit');
                
                if( $weight_unit == 'kg' ){
                    $him_dhl_shipping_weight = array_sum($getallweight);
                    $him_dhl_shipping_weight = $him_dhl_shipping_weight * 1000;
                }else{
                    $him_dhl_shipping_weight = array_sum($getallweight);
                }

            }

            $dhl_product_code                       = get_user_meta($current_user_id, 'dhl_product_code', true);
            $dhl_shipping_address                   = $dhl_order->get_formatted_shipping_address();
            $him_dhl_order_pickup_account           = get_post_meta($dhl_order_id, 'him_dhl_order_pickup_account', true);
            $him_dhl_order_product_code             = get_post_meta($dhl_order_id, 'him_dhl_order_product_code', true);
            $him_dhl_shipping_remark                = get_post_meta($dhl_order_id, 'him_dhl_shipping_remark', true);
            $him_dhl_shipping_package_description   = get_post_meta($dhl_order_id, 'him_dhl_shipping_package_description', true);
            $him_dhl_shipping_handover_method       = get_post_meta($dhl_order_id, 'him_dhl_shipping_handover_method', true);
            $him_dhl_shipping_pickup_date           = get_post_meta($dhl_order_id, 'him_dhl_shipping_pickup_date', true);
            $him_dhl_shipping_companyName           = get_post_meta($dhl_order_id, 'him_dhl_shipping_companyName', true);
            $him_dhl_shipping_buyer_defaultname     = $getOrderproductdata['shipping']['first_name'];
            $him_dhl_shipping_buyer_name            = get_post_meta($dhl_order_id, 'him_dhl_shipping_buyer_name', true);
            $shipping_address_1                     = $getOrderproductdata['shipping']['address_1'];
            $him_dhl_shipping_address_line_one      = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_line_one', true);
            $shipping_address_2                     = $getOrderproductdata['shipping']['address_2'];
            $him_dhl_shipping_address_line_two      = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_line_two', true);
            $him_dhl_shipping_address_line_three    = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_line_three', true);
            $shipping_city                          = $getOrderproductdata['shipping']['city'];
            $him_dhl_shipping_address_city          = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_city', true);
            $shipping_state                         = $getOrderproductdata['shipping']['state'];
            $him_dhl_shipping_address_state         = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_state', true);
            $him_dhl_shipping_address_district      = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_district', true);
            $dhl_country                            = get_user_meta($current_user_id, 'dhl_country', true);
            $him_dhl_shipping_address_country       = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_country', true);
            $shipping_postcode                      = $getOrderproductdata['shipping']['postcode'];
            $him_dhl_shipping_address_postcode      = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_postcode', true);
            $billing_phone                          = $getOrderproductdata['billing']['phone'];
            $him_dhl_shipping_address_phone         = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_phone', true);
            $billing_email                          = $getOrderproductdata['billing']['email'];
            $him_dhl_shipping_address_email         = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_email', true);
            $him_dhl_shipping_address_return_mode   = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_mode', true);

            //New Pickup Account Details here

            $pickupID = $_GET['pickup'];

            if ( !empty( $pickupID ) ) {
                
                $him_dhl_shipping_buyer_defaultname     = get_post_meta($pickupID, '_him_pickup_your_name_field', true);
                $shipping_address_1                     = get_post_meta($pickupID, '_him_address_one_field', true);
                $shipping_address_2                     = get_post_meta($pickupID, '_him_address_two_field', true);
                $him_dhl_shipping_address_line_three    = get_post_meta($pickupID, '_him_address_three_field', true);
                $shipping_city                          = get_post_meta($pickupID, '_him_city_field', true);
                $shipping_state                         = get_post_meta($pickupID, '_him_state_field', true);
                $dhl_countryy                           = get_post_meta($pickupID, '_him_district_field', true);
                $shipping_postcode                      = get_post_meta($pickupID, '_him_postcode_field', true);
                $billing_phone                          = get_post_meta($pickupID, '_him_phone_field', true);
                $billing_email                          = get_post_meta($pickupID, '_him_email_field', true);

                $dhl_account_type                       = get_the_title($pickupID);

            }else{

                $pickupIDarray = array();

                $him_dhl_shipping_buyer_defaultname     = get_post_meta($dhl_pickup_account, '_him_pickup_your_name_field', true);
                $shipping_address_1                     = get_post_meta($dhl_pickup_account, '_him_address_one_field', true);
                $shipping_address_2                     = get_post_meta($dhl_pickup_account, '_him_address_two_field', true);
                $him_dhl_shipping_address_line_three    = get_post_meta($dhl_pickup_account, '_him_address_three_field', true);
                $shipping_city                          = get_post_meta($dhl_pickup_account, '_him_city_field', true);
                $shipping_state                         = get_post_meta($dhl_pickup_account, '_him_state_field', true);
                $dhl_countryy                           = get_post_meta($dhl_pickup_account, '_him_district_field', true);
                $shipping_postcode                      = get_post_meta($dhl_pickup_account, '_him_postcode_field', true);
                $billing_phone                          = get_post_meta($dhl_pickup_account, '_him_phone_field', true);
                $billing_email                          = get_post_meta($dhl_pickup_account, '_him_email_field', true);

                $pickupquery   =   array(
                    'numberposts'   => -1,
                    'post_type'     => 'him_pickup_account',
                    'post_status' => 'publish',
                    'meta_query'    => array(
                        array(
                            'key'       => '_him_default_address_field',
                            'value'     => 'on',
                            'compare'   => 'IN',
                        )
                    )
                
                );
                $flowerposts = new WP_Query($pickupquery);
                if ( $flowerposts->have_posts() ) {
                while ( $flowerposts->have_posts() ) {
                    $flowerposts->the_post();
                    $pickupIDarray[] = get_the_title();
                }
                } else {
                    $pickupIDarray[] = 'DHL eCommerce Asia';
                }
                wp_reset_postdata();

                $dhl_account_type = $pickupIDarray[0];

            }

            if (!empty($productnamearray)) {
                $productnamearray = implode(" ", $productnamearray);
                $productnamearray = substr($productnamearray, 0, 50); // Limit to 50 characters
            } else {
                $productnamearray = '';
            }

            $him_dhl_shipping_remark                = ($him_dhl_shipping_remark) ? $him_dhl_shipping_remark : $productnamearray;
            $him_dhl_shipping_package_description   = ($him_dhl_shipping_package_description) ? $him_dhl_shipping_package_description : $productnamearray;
            $him_dhl_shipping_handover_method       = ($him_dhl_shipping_handover_method) ? $him_dhl_shipping_handover_method : '';
            $him_dhl_shipping_companyName           = ($him_dhl_shipping_companyName) ? $him_dhl_shipping_companyName : $dhl_account_type;
            $him_dhl_order_pickup_account           = ($him_dhl_order_pickup_account) ? $him_dhl_order_pickup_account : $dhl_pickup_account;
            $him_dhl_order_product_code             = ($him_dhl_order_product_code) ? $him_dhl_order_product_code : $dhl_product_code;
            $him_dhl_shipping_pickup_date           = ($him_dhl_shipping_pickup_date) ? $him_dhl_shipping_pickup_date : '';
            $him_dhl_shipping_buyer_name            = ($him_dhl_shipping_buyer_name) ? $him_dhl_shipping_buyer_name : $him_dhl_shipping_buyer_defaultname;
            $him_dhl_shipping_address_line_one      = ($him_dhl_shipping_address_line_one) ? $him_dhl_shipping_address_line_one : $shipping_address_1;
            $him_dhl_shipping_address_line_two      = ($him_dhl_shipping_address_line_two) ? $him_dhl_shipping_address_line_two : $shipping_address_2;
            $him_dhl_shipping_address_line_three    = ($him_dhl_shipping_address_line_three) ? $him_dhl_shipping_address_line_three : '';
            $him_dhl_shipping_address_city          = ($him_dhl_shipping_address_city) ? $him_dhl_shipping_address_city : $shipping_city;
            $him_dhl_shipping_address_state         = ($him_dhl_shipping_address_state) ? $him_dhl_shipping_address_state : $shipping_state;
            $him_dhl_shipping_address_country       = ($him_dhl_shipping_address_country) ? $him_dhl_shipping_address_country : $dhl_country;
            $him_dhl_shipping_address_postcode      = ($him_dhl_shipping_address_postcode) ? $him_dhl_shipping_address_postcode : $shipping_postcode;
            $him_dhl_shipping_address_phone         = ($him_dhl_shipping_address_phone) ? $him_dhl_shipping_address_phone : $billing_phone;
            $him_dhl_shipping_address_email         = ($him_dhl_shipping_address_email) ? $him_dhl_shipping_address_email : $billing_email;
            $him_dhl_shipping_address_return_mode   = ($him_dhl_shipping_address_return_mode) ? $him_dhl_shipping_address_return_mode : '';
            $him_dhl_shipping_address_district      = ($him_dhl_shipping_address_district) ? $him_dhl_shipping_address_district : $dhl_countryy;

            //Return to new address
            $him_dhl_shipping_address_return_company_name   = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_company_name', true);
            $him_dhl_shipping_address_return_buyer_name     = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_buyer_name', true);
            $him_dhl_shipping_address_return_address_one    = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_address_one', true);
            $him_dhl_shipping_address_return_address_two    = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_address_two', true);
            $him_dhl_shipping_address_return_address_three  = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_address_three', true);
            $him_dhl_shipping_address_return_city           = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_city', true);
            $him_dhl_shipping_address_return_state          = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_state', true);
            $him_dhl_shipping_address_return_district       = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_district', true);
            $him_dhl_shipping_address_return_postcode       = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_postcode', true);
            $him_dhl_shipping_address_return_phone          = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_phone', true);
            $him_dhl_shipping_address_return_email          = get_post_meta($dhl_order_id, 'him_dhl_shipping_address_return_email', true);

            $him_dhl_shipping_address_return_company_name   = ($him_dhl_shipping_address_return_company_name) ? $him_dhl_shipping_address_return_company_name : '';
            $him_dhl_shipping_address_return_buyer_name     = ($him_dhl_shipping_address_return_buyer_name) ? $him_dhl_shipping_address_return_buyer_name : '';
            $him_dhl_shipping_address_return_address_one    = ($him_dhl_shipping_address_return_address_one) ? $him_dhl_shipping_address_return_address_one : '';
            $him_dhl_shipping_address_return_address_two    = ($him_dhl_shipping_address_return_address_two) ? $him_dhl_shipping_address_return_address_two : '';
            $him_dhl_shipping_address_return_address_three  = ($him_dhl_shipping_address_return_address_three) ? $him_dhl_shipping_address_return_address_three : '';
            $him_dhl_shipping_address_return_city           = ($him_dhl_shipping_address_return_city) ? $him_dhl_shipping_address_return_city : '';
            $him_dhl_shipping_address_return_state          = ($him_dhl_shipping_address_return_state) ? $him_dhl_shipping_address_return_state : '';
            $him_dhl_shipping_address_return_postcode       = ($him_dhl_shipping_address_return_postcode) ? $him_dhl_shipping_address_return_postcode : '';
            $him_dhl_shipping_address_return_phone          = ($him_dhl_shipping_address_return_phone) ? $him_dhl_shipping_address_return_phone : '';
            $him_dhl_shipping_address_return_email          = ($him_dhl_shipping_address_return_email) ? $him_dhl_shipping_address_return_email : '';
            $him_dhl_shipping_address_return_district       = ($him_dhl_shipping_address_return_district) ? $him_dhl_shipping_address_return_district : $him_dhl_shipping_address_district;

            /*Value Added Services save meta*/
            $him_dhl_shipping_cash_on_delivery              = get_post_meta($dhl_order_id, 'him_dhl_shipping_cash_on_delivery', true);
            $him_dhl_shipping_shipment_value_protection     = get_post_meta($dhl_order_id, 'him_dhl_shipping_shipment_value_protection', true);
            $him_dhl_shipping_shipment_value_ppod           = get_post_meta($dhl_order_id, 'him_dhl_shipping_shipment_value_ppod', true);
            $him_dhl_shipping_open_box                      = get_post_meta($dhl_order_id, 'him_dhl_shipping_open_box', true);
            $him_dhl_shipping_multi_pieces_shipment         = get_post_meta($dhl_order_id, 'him_dhl_shipping_multi_pieces_shipment', true);
            $him_dhl_shipping_single_repeter_group          = get_post_meta($dhl_order_id, 'him_dhl_shipping_single_repeter_group', true);
            $him_dhl_shipping_multi_pieces_complete_del     = get_post_meta($dhl_order_id, 'him_dhl_shipping_multi_pieces_complete_del', true);

            $him_dhl_shipping_cash_on_delivery              = ($him_dhl_shipping_cash_on_delivery) ? $him_dhl_shipping_cash_on_delivery : '';
            $him_dhl_shipping_shipment_value_protection     = ($him_dhl_shipping_shipment_value_protection) ? $him_dhl_shipping_shipment_value_protection : '';
            $him_dhl_shipping_shipment_value_ppod           = ($him_dhl_shipping_shipment_value_ppod) ? $him_dhl_shipping_shipment_value_ppod : '';
            $him_dhl_shipping_open_box                      = ($him_dhl_shipping_open_box) ? $him_dhl_shipping_open_box : '';
            $him_dhl_shipping_multi_pieces_complete_del     = ($him_dhl_shipping_multi_pieces_complete_del) ? $him_dhl_shipping_multi_pieces_complete_del : '';
            $him_dhl_shipping_multi_pieces_shipment         = ($him_dhl_shipping_multi_pieces_shipment) ? $him_dhl_shipping_multi_pieces_shipment : '';

            $him_dhl_shipping_weight_meta_value             = get_post_meta($dhl_order_id, 'him_dhl_shipping_weight', true);
            $him_dhl_shipping_weight                        = ($him_dhl_shipping_weight_meta_value) ? $him_dhl_shipping_weight_meta_value : $him_dhl_shipping_weight;

            $him_dhl_order_lable_form_data_save             = get_post_meta($dhl_order_id, 'him_dhl_order_lable_form_data_save', true);

            echo '<div class="him-shipment-dhl-lable">';
                echo '<div class="shipment-dhl-label-form h-container-fluid h-frm">';
                    echo '<h3>' . esc_html(__('Create shipping label', 'dhl-ecommerce-apac')) . '</h3>';
                    ?>

    					<!-- Pickup Account -->
    					<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="him_dhl_create_shipping_lable_form" name="him_dhl_create_shipping_lable_form" >

    						<div class="h-row" style="opacity:0;">
    							<div class="h-col-12 h-col-sm-12">
    								<div class="h-frm-gp form-group">
    									<label class="h-mb-0" for="him_dhl_order_lable_form_data_save" style="cursor:default;"><?php esc_html_e('Check this checkbox if you want to create lable (required)', 'dhl-ecommerce-apac');?></label>
    									<input type="checkbox" style="cursor:default;" name="him_dhl_order_lable_form_data_save" id="him_dhl_order_lable_form_data_save" value="<?php echo esc_attr('yes'); ?>" <?php checked($him_dhl_order_lable_form_data_save, 'yes');?> />
    								</div>
    							</div>
    						</div>

    						<div class="h-row">
    							<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_order_pickup_account"><?php esc_html_e('Pickup Account', 'dhl-ecommerce-apac');?></label>
    									<select class="form-control" name="him_dhl_order_pickup_account" id="him_dhl_order_pickup_account">
        									<option value=""><?php esc_html_e('Please select', 'dhl-ecommerce-apac');?></option>
        									<?php

                                                $him_pickup_account_args = array(
                                                    'post_type' => 'him_pickup_account',
                                                    'order' => 'ASC',
                                                    'post_status' => 'publish',
                                                    'numberposts' => -1,
                                                );
                                                $him_pickup_account = get_posts($him_pickup_account_args);
                                                foreach ($him_pickup_account as $post):
                                                    setup_postdata($post);
                                                    $him_pickup_account_field = get_post_meta($post->ID, '_him_pickup_account_field', true);
                                                    $him_pickup_your_name_field = get_post_meta($post->ID, '_him_pickup_your_name_field', true);
                                                    if ( !empty( $pickupID ) ) {
                                                        $selectedval = ($post->ID == $pickupID) ? ' selected="selected"' : '';
                                                    }else{
                                                        $selectedval = ($him_dhl_order_pickup_account == $post->ID) ? ' selected="selected"' : '';
                                                    }
                                                    ?><option value="<?php echo esc_attr($post->ID); ?>" <?php echo esc_attr($selectedval); ?>><?php echo esc_html($him_pickup_your_name_field); ?> - <?php echo esc_html($him_pickup_account_field); ?></option>
                                                <?php endforeach;
                                            ?>
    								    </select>
    								</div>
    							</div>

    							<!-- Shipment ID -->
    							<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_order_shipment_id"><?php esc_html_e('Shipment ID', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" readonly type="text" name="him_dhl_order_shipment_id" id="him_dhl_order_shipment_id" value="<?php echo esc_attr($dhl_prefix . $dhl_order_id); ?>">
    								</div>
    							</div>

    							<!-- Product Code -->
    							<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_order_product_code"><?php esc_html_e('Product Code', 'dhl-ecommerce-apac');?></label>
    									<?php
                                            $dhl_him_btn_disable_css = '';
                                            $him_dhl_edit_shipping_lable = get_post_meta($dhl_order_id, 'him_dhl_edit_shipping_lable', true);

                                            if ($him_dhl_edit_shipping_lable == 1) {
                                                $dhl_him_btn_disable_css = ' add-disable-css';
                                            }
                                        ?>
    									<select class="form-control<?php echo esc_html($dhl_him_btn_disable_css); ?>" id="him_dhl_order_product_code" name="him_dhl_order_product_code">
    										<option value="" <?php selected($him_dhl_order_product_code, '');?>><?php esc_html_e('Please select', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('PDO'); ?>" <?php selected($him_dhl_order_product_code, 'PDO');?>><?php esc_html_e('Parcel Domestic', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('PDR'); ?>" <?php selected($him_dhl_order_product_code, 'PDR');?>><?php esc_html_e('DHL Parcel Return', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('PDE'); ?>" <?php selected($him_dhl_order_product_code, 'PDE');?>><?php esc_html_e('Parcel Domestic Expedited', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('DDO'); ?>" <?php selected($him_dhl_order_product_code, 'DDO');?>><?php esc_html_e('Document Domestic', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('SDP'); ?>" <?php selected($him_dhl_order_product_code, 'SDP');?>><?php esc_html_e('DHL Parcel Metro', 'dhl-ecommerce-apac');?></option>
    									</select>
    								</div>
    							</div>

    						</div>


    						<!-- Value Added Services Row -->
    						<div class="h-row">

    							<div class="h-col-6">

    								<h3><?php esc_html_e('Value Added Services', 'dhl-ecommerce-apac');?></h3>

    								<!-- Cash on Delivery -->
    								<?php
                                    
                                        //Check COD Payment method order
                                        $payment_method  = get_post_meta($dhl_order_id, '_payment_method', true);

                                        // Get the COD settings
                                        $cod_settings = get_option('woocommerce_cod_settings');

                                        if ($cod_settings['enabled'] === 'yes' && $payment_method == 'cod') {
                                            ?>
                                            <div class="h-mb-3 h-row">
                                                <div class="h-col-4 h-align-self-center">
                                                    <label for="him_dhl_shipping_cash_on_delivery"><?php esc_html_e('Cash on Delivery', 'dhl-ecommerce-apac');?></label>
                                                </div>
                                                <div class="h-col-8">
                                                    <div class="h-d-flex">
                                                        <label class="switch h-mb-0">
                                                            <input type="checkbox" class="him-dhl-cashon-delievry">
                                                            <span class="slider round"></span>
                                                        </label>
                                                        <?php
                                                            if ( empty( $him_dhl_shipping_cash_on_delivery ) && empty( $him_dhl_order_lable_form_data_save ) ) {
                                                                ?>
                                                                <input class="form-control h-ml-3" type="text" name="him_dhl_shipping_cash_on_delivery" id="him_dhl_shipping_cash_on_delivery" value="<?php echo $getOrderproductmname->get_total(); ?>" style="display: none;">
                                                                <?php
                                                            }else{
                                                                ?>
                                                                <input class="form-control h-ml-3" type="text" name="him_dhl_shipping_cash_on_delivery" id="him_dhl_shipping_cash_on_delivery" value="<?php echo esc_attr($him_dhl_shipping_cash_on_delivery); ?>" style="display: none;">
                                                                <?php
                                                            }
                                                        ?> 
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }else{
                                            ?>
                                            <div class="h-mb-3 h-row">
                                                <div class="h-col-4 h-align-self-center">
                                                    <label for="him_dhl_shipping_cash_on_delivery"><?php esc_html_e('Cash on Delivery', 'dhl-ecommerce-apac');?></label>
                                                </div>
                                                <div class="h-col-8">
                                                    <div class="h-d-flex">
                                                        <label class="switch h-mb-0">
                                                            <input type="checkbox" class="him-dhl-cashon-delievry">
                                                            <span class="slider round"></span>
                                                        </label>
                                                        <input class="form-control h-ml-3" type="text" name="him_dhl_shipping_cash_on_delivery" id="him_dhl_shipping_cash_on_delivery" value="<?php echo esc_attr($him_dhl_shipping_cash_on_delivery); ?>" style="display: none;">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    ?>

    								<!-- Shipment Value Protection -->
    								<div class="h-mb-3 h-row">
    									<div class="h-col-4 h-align-self-center">
    										<label for="him_dhl_shipping_shipment_value_protection"><?php esc_html_e('Shipment Value Protection', 'dhl-ecommerce-apac');?></label>
    										</div>
    									<div class="h-col-8">
    										<div class="h-d-flex">
    											<label class="switch h-mb-0">
    												<input type="checkbox" class="him-dhl-shipment-protection">
    												<span class="slider round"></span>
    											</label>
    											<input class="form-control h-ml-3" type="text" name="him_dhl_shipping_shipment_value_protection" id="him_dhl_shipping_shipment_value_protection" value="<?php echo esc_attr($him_dhl_shipping_shipment_value_protection); ?>" style="display: none;">
    										</div>
    									</div>
    								</div>

    								<!-- Paper Proof of Delivery (PPOD) -->
    								<div class="h-mb-3 h-row">
    									<div class="h-col-4 h-align-self-center">
    										<label><?php esc_html_e('Paper Proof of Delivery (PPOD)', 'dhl-ecommerce-apac');?></label>
    									</div>
    									<div class="h-col-8 him-dhl-ppod-div">
    										<input class="h-d-none" type="checkbox" name="him_dhl_shipping_shipment_value_ppod" id="him_dhl_shipping_shipment_value_ppod" value="<?php echo esc_attr('yes'); ?>" <?php checked($him_dhl_shipping_shipment_value_ppod, 'yes');?>/><label for="him_dhl_shipping_shipment_value_ppod"><?php esc_html_e('Paper Proof of Delivery (PPOD)', 'dhl-ecommerce-apac');?></label>
    									</div>
    								</div>

    								<!-- Open Box -->
    								<div class="h-mb-3 h-row">
    								   <div class="h-col-4 h-align-self-center">
    										<p class="h-my-0 txt-b"><?php esc_html_e('Open Box', 'dhl-ecommerce-apac');?></p>
    								   </div>
    								   <div class="h-col-8 him-dhl-ppod-div">
    										<input class="h-d-none" type="checkbox" name="him_dhl_shipping_open_box" id="him_dhl_shipping_open_box" value="<?php echo esc_attr('yes'); ?>" <?php checked($him_dhl_shipping_open_box, 'yes');?>/><label for="him_dhl_shipping_open_box"><?php esc_html_e('Open Box', 'dhl-ecommerce-apac');?></label>
    									</div>
    								</div>

    								<!-- Multi Pieces Shipment  -->
    								<div class="h-mb-3 h-row">
    								   <div class="h-col-4 h-align-self-center">
    										<p class="h-my-0 txt-b"><?php esc_html_e('Multi Pieces Shipment', 'dhl-ecommerce-apac');?></p>
    								   </div>
    								   <div class="h-col-8 him-dhl-ppod-div">
    										<input class="h-d-none" type="checkbox" name="him_dhl_shipping_multi_pieces_shipment" id="him_dhl_shipping_multi_pieces_shipment" value="<?php echo esc_attr('true'); ?>" <?php checked($him_dhl_shipping_multi_pieces_shipment, 'true');?>/><label for="him_dhl_shipping_multi_pieces_shipment"><?php esc_html_e('Multi Pieces Shipment', 'dhl-ecommerce-apac');?></label>
    									</div>
    								</div>
    							</div>

    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<h3><?php esc_html_e('Consignee Address', 'dhl-ecommerce-apac');?></h3>
    									<?php
                                            if (!empty($dhl_shipping_address)) {
                                                _e( $dhl_shipping_address );
                                            }
                                        ?>
    								</div>
    							</div>

    							<!-- Delivery Option -->
    							<div class="h-col-6 him-dhl-delivery-option" style="display:none;">
    								<div class="h-row h-mb-3">
    									<div class="h-col-4 h-align-self-center">
    										<label class="h-mb-0" for="him_dhl_shipping_multi_pieces_delivery_option"><?php esc_html_e('Delivery Option', 'dhl-ecommerce-apac');?></label>
    									</div>
    									<div class="h-col-8">
    										<div class="h-mb-1">
    											<input type="radio" id="him-dhl-completed-del" name="him_dhl_shipping_multi_pieces_complete_del" value="<?php echo esc_attr('C'); ?>" <?php checked($him_dhl_shipping_multi_pieces_complete_del, 'C');?> checked>
    											<label for="him-dhl-completed-del"><?php esc_html_e('Complete Delivery', 'dhl-ecommerce-apac');?></label><br>
    										</div>
    										<input type="radio" id="him-dhl-partial-del" name="him_dhl_shipping_multi_pieces_complete_del" value="<?php echo esc_attr('P'); ?>" <?php checked($him_dhl_shipping_multi_pieces_complete_del, 'P');?>>
    										<label for="him-dhl-partial-del"><?php esc_html_e('Partial Delivery', 'dhl-ecommerce-apac');?></label><br>
    									</div>
    								</div>
    							</div>

    							<!-- Add Repeated field -->
    							<div class="h-col-12 him-dhl-repeated-multi-shipment" style="display:none;">
    								<table id="dhl-him-repeatable-fieldset-one" width="100%">
    									<tbody class="h-text-left">
    										<tr>
    											<th><?php esc_html_e('Piece Description', 'dhl-ecommerce-apac');?></th>
    											<th><?php esc_html_e('Shipment Weight(G)', 'dhl-ecommerce-apac');?></th>
    											<th><?php esc_html_e('Billing Ref 1', 'dhl-ecommerce-apac');?></th>
    											<th><?php esc_html_e('Billing Ref 2', 'dhl-ecommerce-apac');?></th>
    											<th><?php esc_html_e('Shipment Insurance', 'dhl-ecommerce-apac');?></th>
    											<th><?php esc_html_e('Cash on Delivery', 'dhl-ecommerce-apac');?></th>
    										  </tr>
    										<?php
                                                if ($him_dhl_shipping_single_repeter_group):
                                                    foreach ($him_dhl_shipping_single_repeter_group as $field) {
                                                        ?>
                                                            <tr>
                                                                <td><input class="form-control" type="text" name="him_dhl_piecedescription[]" value="<?php if ($field['him_dhl_piecedescription'] != '') {echo esc_attr($field['him_dhl_piecedescription']);}?>" placeholder="<?php esc_html_e('Piece Description', 'dhl-ecommerce-apac');?>" /></td>
                                                                <td><input class="form-control dhl-him-shipment-weight" type="text" name="him_dhl_shipment_weight[]" value="<?php if ($field['him_dhl_shipment_weight'] != '') {echo esc_attr($field['him_dhl_shipment_weight']);}?>" placeholder="<?php esc_html_e('Shipment Weight(G)', 'dhl-ecommerce-apac');?>" /></td>
                                                                <td><input class="form-control" type="text" name="him_dhl_shipment_billing_ref_1[]" value="<?php if ($field['him_dhl_shipment_billing_ref_1'] != '') {echo esc_attr($field['him_dhl_shipment_billing_ref_1']);}?>" placeholder="<?php esc_html_e('Billing Ref 1', 'dhl-ecommerce-apac');?>" /></td>
                                                                <td><input class="form-control" type="text" name="him_dhl_shipment_billing_ref_2[]" value="<?php if ($field['him_dhl_shipment_billing_ref_2'] != '') {echo esc_attr($field['him_dhl_shipment_billing_ref_2']);}?>" placeholder="<?php esc_html_e('Billing Ref 2', 'dhl-ecommerce-apac');?>" /></td>
                                                                <td><input class="dhl-him-shipment-insurance form-control" type="text" name="him_dhl_shipment_billing_shipment_insurance[]" value="<?php if ($field['him_dhl_shipment_billing_shipment_insurance'] != '') {echo esc_attr($field['him_dhl_shipment_billing_shipment_insurance']);}?>" placeholder="<?php esc_html_e('Shipment Insurance', 'dhl-ecommerce-apac');?>" disabled /></td>
                                                                <td><input class="dhl-him-cash-on-del form-control" type="text" name="him_dhl_shipment_billing_shipment_cash_on_del[]" value="<?php if ($field['him_dhl_shipment_billing_shipment_cash_on_del'] != '') {echo esc_attr($field['him_dhl_shipment_billing_shipment_cash_on_del']);}?>" placeholder="<?php esc_html_e('Cash on Delivery', 'dhl-ecommerce-apac');?>" disabled /></td>
                                                                <td><a class="button dhl-him-remove-row" href="javascript:void(0)"><?php esc_html_e('Remove', 'dhl-ecommerce-apac');?></a></td>
                                                            </tr>
                                                        <?php
                                                } 
                                            else :
                                                ?>  
    											<tr>
    												<td><input class="form-control" type="text" name="him_dhl_piecedescription[]" placeholder="<?php esc_html_e('Piece Description', 'dhl-ecommerce-apac');?>"/></td>
    												<td><input class="form-control dhl-him-shipment-weight" type="text" name="him_dhl_shipment_weight[]" value="" placeholder="<?php esc_html_e('Shipment Weight(G)', 'dhl-ecommerce-apac');?>" /></td>
    												<td><input class="form-control" type="text" name="him_dhl_shipment_billing_ref_1[]" value="" placeholder="<?php esc_html_e('Billing Ref 1', 'dhl-ecommerce-apac');?>" /></td>
    												<td><input class="form-control" type="text" name="him_dhl_shipment_billing_ref_2[]" value="" placeholder="<?php esc_html_e('Billing Ref 2', 'dhl-ecommerce-apac');?>" /></td>
    												<td><input class="form-control dhl-him-shipment-insurance" type="text" name="him_dhl_shipment_billing_shipment_insurance[]" value="" placeholder="<?php esc_html_e('Shipment Insurance', 'dhl-ecommerce-apac');?>" disabled /></td>
    												<td><input class="form-control dhl-him-cash-on-del" type="text" name="him_dhl_shipment_billing_shipment_cash_on_del[]" value="" placeholder="<?php esc_html_e('Cash on Delivery', 'dhl-ecommerce-apac');?>" disabled /></td>
    												<td><a class="button  cmb-remove-row-button button-disabled" href="javascript:void(0)"><?php esc_html_e('Remove', 'dhl-ecommerce-apac');?></a></td>
    											</tr>
    										<?php endif;?>
    										<tr class="empty-row dhl-him-custom-repeter-text" style="display: none">
    											<td><input class="form-control" type="text" name="him_dhl_piecedescription[]" placeholder="<?php esc_html_e('Piece Description', 'dhl-ecommerce-apac');?>"/></td>
    											<td><input class="form-control dhl-him-shipment-weight" type="text" name="him_dhl_shipment_weight[]" value="" placeholder="<?php esc_html_e('Shipment Weight(G)', 'dhl-ecommerce-apac');?>"/></td>
    											<td><input class="form-control" type="text" name="him_dhl_shipment_billing_ref_1[]" value="" placeholder="<?php esc_html_e('Billing Ref 1', 'dhl-ecommerce-apac');?>"/></td>
    											<td><input class="form-control" type="text" name="him_dhl_shipment_billing_ref_2[]" value="" placeholder="<?php esc_html_e('Billing Ref 2', 'dhl-ecommerce-apac');?>"/></td>
    											<td><input class="form-control dhl-him-shipment-insurance" type="text" name="him_dhl_shipment_billing_shipment_insurance[]" value="" placeholder="<?php esc_html_e('Shipment Insurance', 'dhl-ecommerce-apac');?>" disabled /></td>
    											<td><input class="form-control dhl-him-cash-on-del" type="text" name="him_dhl_shipment_billing_shipment_cash_on_del[]" value="" placeholder="<?php esc_html_e('Cash on Delivery', 'dhl-ecommerce-apac');?>" disabled /></td>
    											<td><a class="button dhl-him-remove-row" href="javascript:void(0)"><?php esc_html_e('Remove', 'dhl-ecommerce-apac');?></a></td>
    										</tr>
    									</tbody>
    								</table>
    								<p><a id="him-dhl-add-row" class="button" href="javascript:void(0)"><?php esc_html_e('Add Row', 'dhl-ecommerce-apac');?></a></p>
    							</div>

    						</div>


    						<!-- Shipment Details Row -->
    						<div class="h-row">

    							<div class="h-col-12">
    								<hr>
    							</div>
    							<div class="h-col-12">
    								<h3 class="h-mt-0"><?php esc_html_e('Shipment Details', 'dhl-ecommerce-apac');?></h3>
    							</div>

    							<!-- Shipment Weight (GM) -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_weight"><?php esc_html_e('Shipment Weight (GM)', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" id="him_dhl_shipping_weight" type="text" name="him_dhl_shipping_weight" value="<?php echo esc_attr($him_dhl_shipping_weight); ?>">
    								</div>
    							</div>

    							<!-- Currency -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_currency"><?php esc_html_e('Currency', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_currency" id="him_dhl_shipping_currency" value="<?php echo esc_attr(get_woocommerce_currency_symbol()); ?>" readonly />
    								</div>
    							</div>

    							<!-- Package Description -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_package_description"><?php esc_html_e('Package Description', 'dhl-ecommerce-apac');?></label>
    									<textarea class="form-control" id="him_dhl_shipping_package_description" name="him_dhl_shipping_package_description" rows="4" cols="50" maxlength="50"><?php echo esc_textarea($him_dhl_shipping_package_description); ?></textarea>
    								</div>
    							</div>

    							<!-- Remarks -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_remark"><?php esc_html_e('Remarks', 'dhl-ecommerce-apac');?></label>
    									<textarea class="form-control" id="him_dhl_shipping_remark" name="him_dhl_shipping_remark" rows="4" cols="50" maxlength="200"><?php echo esc_textarea($him_dhl_shipping_remark); ?></textarea>
    								</div>
    							</div>

    							<!-- Handover Method -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_handover_method"><?php esc_html_e('Handover Method', 'dhl-ecommerce-apac');?></label>
    									<select class="form-control" id="him_dhl_shipping_handover_method" name="him_dhl_shipping_handover_method">
    										<option value="" <?php selected($him_dhl_shipping_handover_method, '');?>><?php esc_html_e('Please select', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('1'); ?>" <?php selected($him_dhl_shipping_handover_method, '1');?> selected><?php esc_html_e('Drop off', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('2'); ?>" <?php selected($him_dhl_shipping_handover_method, '2');?>><?php esc_html_e('Pickup', 'dhl-ecommerce-apac');?></option>
    									</select>
    								</div>
    							</div>

    							<!-- Remarks -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_pickup_date"><?php esc_html_e('Pickup Date', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="date" name="him_dhl_shipping_pickup_date" value="<?php echo esc_attr($him_dhl_shipping_pickup_date); ?>" readonly>
    								</div>
    							</div>

    						</div>

    						<!-- Shipper Address Row -->
    						<div class="h-row">

    							<div class="h-col-12">
    								<hr>
    							</div>
    							<div class="h-col-12">
    								<h3 class="h-mt-0"><?php esc_html_e('Shipper Address', 'dhl-ecommerce-apac');?></h3>
    							</div>

    							<!-- CompanyName -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_companyName"><?php esc_html_e('Company Name', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_companyName" value="<?php echo esc_attr($him_dhl_shipping_companyName); ?>" readonly>
    								</div>
    							</div>

    							<!-- Name -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_buyer_name"><?php esc_html_e('Name', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_buyer_name" value="<?php echo esc_attr($him_dhl_shipping_buyer_name); ?>" readonly>
    								</div>
    							</div>

    							<!-- Address Line 1 -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_line_one"><?php esc_html_e('Address Line 1', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_line_one" value="<?php echo esc_attr($him_dhl_shipping_address_line_one); ?>" readonly>
    								</div>
    							</div>

    							<!-- Address Line 2 -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_line_two"><?php esc_html_e('Address Line 2', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_line_two" value="<?php echo esc_attr($him_dhl_shipping_address_line_two); ?>" readonly>
    								</div>
    							</div>

    							<!-- Address Line 3 -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_line_three"><?php esc_html_e('Address Line 3', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_line_three" value="<?php echo esc_attr($him_dhl_shipping_address_line_three); ?>" readonly>
    								</div>
    							</div>

    							<!-- City -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_city"><?php esc_html_e('City', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_city" value="<?php echo esc_attr($him_dhl_shipping_address_city); ?>" readonly>
    								</div>
    							</div>

    							<!-- State -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_state"><?php esc_html_e('State', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_state" value="<?php echo esc_attr($him_dhl_shipping_address_state); ?>" readonly>
    								</div>
    							</div>

    							<!-- District -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_district"><?php esc_html_e('District', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_district" value="<?php echo esc_attr($him_dhl_shipping_address_district); ?>" readonly>
    								</div>
    							</div>

    							<!-- Country -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_country"><?php esc_html_e('Country', 'dhl-ecommerce-apac');?></label>
    									<select class="form-control" id="him_dhl_shipping_address_country" name="him_dhl_shipping_address_country">
    										<option value="" <?php selected($him_dhl_shipping_address_country, '');?>><?php esc_html_e('Please select', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('malaysia'); ?>" <?php selected($him_dhl_shipping_address_country, 'malaysia');?>><?php esc_html_e('Malaysia', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('thailand'); ?>" <?php selected($him_dhl_shipping_address_country, 'thailand');?>><?php esc_html_e('Thailand', 'dhl-ecommerce-apac');?></option>
    									</select>
    								</div>
    							</div>

    							<!-- Postcode -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_postcode"><?php esc_html_e('Postcode', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_postcode" value="<?php echo esc_attr($him_dhl_shipping_address_postcode); ?>" readonly>
    								</div>
    							</div>

    							<!-- Phone -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_phone"><?php esc_html_e('Phone', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_phone" value="<?php echo esc_attr($him_dhl_shipping_address_phone); ?>" readonly>
    								</div>
    							</div>

    							<!-- Email -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_email"><?php esc_html_e('Email', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_email" value="<?php echo esc_attr($him_dhl_shipping_address_email); ?>" readonly>
    								</div>
    							</div>

    						</div>

    						<!-- Return Address Row -->

    						<div class="h-row">

    							<div class="h-col-12">
    								<hr>
    								<h3 class="h-mt-0"><?php esc_html_e('Return Address', 'dhl-ecommerce-apac');?></h3>
    							</div>

    							<!-- Return Mode -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_mode"><?php esc_html_e('Return Mode', 'dhl-ecommerce-apac');?></label>
    									<select class="form-control" id="him_dhl_shipping_address_return_mode" name="him_dhl_shipping_address_return_mode">
    										<option value="<?php echo esc_attr('01'); ?>" <?php selected($him_dhl_shipping_address_return_mode, '01');?>><?php esc_html_e('Return to Registered Address', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('02'); ?>" disabled <?php selected($him_dhl_shipping_address_return_mode, '02');?>><?php esc_html_e('Return to Pickup Address', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('03'); ?>" <?php selected($him_dhl_shipping_address_return_mode, '03');?>><?php esc_html_e('Return to New Address', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('04'); ?>" <?php selected($him_dhl_shipping_address_return_mode, '04');?>><?php esc_html_e('Abandon', 'dhl-ecommerce-apac');?></option>
    									</select>
    								</div>
    							</div>

    						</div>


    						<!-- Return Address -->

    						<div class="h-row him-dhl-return-form" style="display:none;">

    							<!-- Return Address -->
    							<div class="h-col-12 return-address">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_address"><?php esc_html_e('Return Address', 'dhl-ecommerce-apac');?></label>
    								</div>
    							</div>

    							<!-- Return CompanyName -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_company_name"><?php esc_html_e('CompanyName', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_company_name" value="<?php echo esc_attr($him_dhl_shipping_address_return_company_name); ?>">
    								</div>
    							</div>

    							<!-- Return Name -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_buyer_name"><?php esc_html_e('Name', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_buyer_name" value="<?php echo esc_attr($him_dhl_shipping_address_return_buyer_name); ?>">
    								</div>
    							</div>

    							<!-- Return Address Line 1 -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_address_one"><?php esc_html_e('Address Line 1', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_address_one" value="<?php echo esc_attr($him_dhl_shipping_address_return_address_one); ?>">
    								</div>
    							</div>

    							<!-- Return Address Line 2 -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_address_two"><?php esc_html_e('Address Line 2', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_address_two" value="<?php echo esc_attr($him_dhl_shipping_address_return_address_two); ?>">
    								</div>
    							</div>

    							<!-- Return Address Line 3 -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_address_three"><?php esc_html_e('Address Line 3', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_address_three" value="<?php echo esc_attr($him_dhl_shipping_address_return_address_three); ?>">
    								</div>
    							</div>

    							<!-- Return City -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_city"><?php esc_html_e('City', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_city" value="<?php echo esc_attr($him_dhl_shipping_address_return_city); ?>">
    								</div>
    							</div>

    							<!-- Return State -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_state"><?php esc_html_e('State', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_state" value="<?php echo esc_attr($him_dhl_shipping_address_return_state); ?>">
    								</div>
    							</div>

    							<!-- Return District -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_district"><?php esc_html_e('District', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_district" value="<?php echo esc_attr($him_dhl_shipping_address_return_district); ?>">
    								</div>
    							</div>

    							<!-- Return Country -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_country"><?php esc_html_e('Country', 'dhl-ecommerce-apac');?></label>
    									<select class="form-control" id="him_dhl_shipping_address_return_country" name="him_dhl_shipping_address_return_country">
    										<option value="<?php echo esc_attr('malaysia'); ?>" selected><?php esc_html_e('Malaysia', 'dhl-ecommerce-apac');?></option>
    									</select>
    								</div>
    							</div>

    							<!-- Return Postcode -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_postcode"><?php esc_html_e('Postcode', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_postcode" value="<?php echo esc_attr($him_dhl_shipping_address_return_postcode); ?>">
    								</div>
    							</div>

    							<!-- Return Phone -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_phone"><?php esc_html_e('Phone', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_phone" value="<?php echo esc_attr($him_dhl_shipping_address_return_phone); ?>">
    								</div>
    							</div>

    							<!-- Return Email -->
    							<div class="h-col-6">
    								<div class="h-frm-gp form-group">
    									<label for="him_dhl_shipping_address_return_email"><?php esc_html_e('Email', 'dhl-ecommerce-apac');?></label>
    									<input class="form-control" type="text" name="him_dhl_shipping_address_return_email" value="<?php echo esc_attr($him_dhl_shipping_address_return_email); ?>">
    								</div>
    							</div>

    						</div>

    						<!-- Genarate Lable Button -->
    						<div class="h-row h-mb-2">
    							<div class="h-col-12">
    								<hr>
    								<?php
                                        $dhl_him_buttton_lable_name         = 'Create Label';
                                        $him_dhl_get_edit_shipping_lable    = sanitize_text_field( $_GET['editlable'] );

                                        if (isset( $him_dhl_get_edit_shipping_lable )) {
                                            if ($him_dhl_get_edit_shipping_lable == 'true') {
                                                $dhl_him_buttton_lable_name = 'Update Label';
                                            }
                                        }
                                    ?>
    								<input type="submit" class="button dhl_him_create_lable him_dhl_first_create_lable" name="dhl_him_create_lable" value="<?php echo esc_attr($dhl_him_buttton_lable_name); ?>">
    							</div>
    						</div>

    					</form>

    				<?php
                echo '</div>';
            echo '</div>';

        }

        /**
         * Save the meta box for shipment info on the order page
         *
         */
        public function him_order_shipping_lable_save_meta_box($post_id, $post = null)
        {
            //Sanitizing Data list here and pass the variable value in update_post_meta.
            $him_dhl_order_lable_form_data_save             = sanitize_text_field($_POST['him_dhl_order_lable_form_data_save']);
            $him_dhl_order_pickup_account                   = sanitize_text_field($_POST['him_dhl_order_pickup_account']);
            $him_dhl_order_shipment_id                      = sanitize_text_field($_POST['him_dhl_order_shipment_id']);
            $him_dhl_order_product_code                     = sanitize_text_field($_POST['him_dhl_order_product_code']);
            $him_dhl_shipping_remark                        = sanitize_text_field($_POST['him_dhl_shipping_remark']);
            $him_dhl_shipping_package_description           = sanitize_text_field($_POST['him_dhl_shipping_package_description']);
            $him_dhl_shipping_handover_method               = sanitize_text_field($_POST['him_dhl_shipping_handover_method']);
            $him_dhl_shipping_currency                      = sanitize_text_field($_POST['him_dhl_shipping_currency']);
            $him_dhl_shipping_pickup_date                   = sanitize_text_field($_POST['him_dhl_shipping_pickup_date']);
            $him_dhl_shipping_companyName                   = sanitize_text_field($_POST['him_dhl_shipping_companyName']);
            $him_dhl_shipping_buyer_name                    = sanitize_text_field($_POST['him_dhl_shipping_buyer_name']);
            $him_dhl_shipping_address_line_one              = sanitize_text_field($_POST['him_dhl_shipping_address_line_one']);
            $him_dhl_shipping_address_line_two              = sanitize_text_field($_POST['him_dhl_shipping_address_line_two']);
            $him_dhl_shipping_address_line_three            = sanitize_text_field($_POST['him_dhl_shipping_address_line_three']);
            $him_dhl_shipping_address_city                  = sanitize_text_field($_POST['him_dhl_shipping_address_city']);
            $him_dhl_shipping_address_state                 = sanitize_text_field($_POST['him_dhl_shipping_address_state']);
            $him_dhl_shipping_address_district              = sanitize_text_field($_POST['him_dhl_shipping_address_district']);
            $him_dhl_shipping_address_country               = sanitize_text_field($_POST['him_dhl_shipping_address_country']);
            $him_dhl_shipping_address_postcode              = sanitize_text_field($_POST['him_dhl_shipping_address_postcode']);
            $him_dhl_shipping_address_phone                 = sanitize_text_field($_POST['him_dhl_shipping_address_phone']);
            $him_dhl_shipping_address_email                 = sanitize_text_field($_POST['him_dhl_shipping_address_email']);
            $him_dhl_shipping_address_return_mode           = sanitize_text_field($_POST['him_dhl_shipping_address_return_mode']);
            $him_dhl_shipping_address_return_company_name   = sanitize_text_field($_POST['him_dhl_shipping_address_return_company_name']);
            $him_dhl_shipping_address_return_buyer_name     = sanitize_text_field($_POST['him_dhl_shipping_address_return_buyer_name']);
            $him_dhl_shipping_address_return_address_one    = sanitize_text_field($_POST['him_dhl_shipping_address_return_address_one']);
            $him_dhl_shipping_address_return_address_two    = sanitize_text_field($_POST['him_dhl_shipping_address_return_address_two']);
            $him_dhl_shipping_address_return_address_three  = sanitize_text_field($_POST['him_dhl_shipping_address_return_address_three']);
            $him_dhl_shipping_address_return_city           = sanitize_text_field($_POST['him_dhl_shipping_address_return_city']);
            $him_dhl_shipping_address_return_state          = sanitize_text_field($_POST['him_dhl_shipping_address_return_state']);
            $him_dhl_shipping_address_return_district       = sanitize_text_field($_POST['him_dhl_shipping_address_return_district']);
            $him_dhl_shipping_address_return_country        = sanitize_text_field($_POST['him_dhl_shipping_address_return_country']);
            $him_dhl_shipping_address_return_postcode       = sanitize_text_field($_POST['him_dhl_shipping_address_return_postcode']);
            $him_dhl_shipping_address_return_phone          = sanitize_text_field($_POST['him_dhl_shipping_address_return_phone']);
            $him_dhl_shipping_address_return_email          = sanitize_email($_POST['him_dhl_shipping_address_return_email']);
            $him_dhl_shipping_cash_on_delivery              = sanitize_text_field($_POST['him_dhl_shipping_cash_on_delivery']);
            $him_dhl_shipping_shipment_value_protection     = sanitize_text_field($_POST['him_dhl_shipping_shipment_value_protection']);
            $him_dhl_shipping_shipment_value_ppod           = sanitize_text_field($_POST['him_dhl_shipping_shipment_value_ppod']);
            $him_dhl_shipping_open_box                      = sanitize_text_field($_POST['him_dhl_shipping_open_box']);
            $him_dhl_shipping_multi_pieces_shipment         = sanitize_text_field($_POST['him_dhl_shipping_multi_pieces_shipment']);

            update_post_meta($post_id, 'him_dhl_order_lable_form_data_save', $him_dhl_order_lable_form_data_save);

            $him_dhl_order_lable_form_data_save = get_post_meta($post_id, 'him_dhl_order_lable_form_data_save', true);

            if ($him_dhl_order_lable_form_data_save == 'yes') {

                if (isset($him_dhl_order_pickup_account)) {

                    update_post_meta($post_id, 'him_dhl_order_pickup_account', $him_dhl_order_pickup_account);
                }

                if (isset($him_dhl_order_shipment_id)) {

                    update_post_meta($post_id, 'him_dhl_order_shipment_id', $him_dhl_order_shipment_id);
                }

                if (isset($him_dhl_order_product_code)) {

                    update_post_meta($post_id, 'him_dhl_order_product_code', $him_dhl_order_product_code);
                }

                if (isset($him_dhl_shipping_remark)) {

                    update_post_meta($post_id, 'him_dhl_shipping_remark', $him_dhl_shipping_remark);
                }

                if (isset($him_dhl_shipping_package_description)) {

                    update_post_meta($post_id, 'him_dhl_shipping_package_description', $him_dhl_shipping_package_description);
                }

                if (isset($him_dhl_shipping_handover_method)) {

                    update_post_meta($post_id, 'him_dhl_shipping_handover_method', $him_dhl_shipping_handover_method);
                }

                if (isset($him_dhl_shipping_currency)) {

                    update_post_meta($post_id, 'him_dhl_shipping_currency', $him_dhl_shipping_currency);
                }

                if (isset($him_dhl_shipping_pickup_date)) {

                    update_post_meta($post_id, 'him_dhl_shipping_pickup_date', $him_dhl_shipping_pickup_date);
                }

                if (isset($him_dhl_shipping_companyName)) {

                    update_post_meta($post_id, 'him_dhl_shipping_companyName', $him_dhl_shipping_companyName);
                }

                if (isset($him_dhl_shipping_buyer_name)) {

                    update_post_meta($post_id, 'him_dhl_shipping_buyer_name', $him_dhl_shipping_buyer_name);
                }

                if (isset($him_dhl_shipping_address_line_one)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_line_one', $him_dhl_shipping_address_line_one);
                }

                if (isset($him_dhl_shipping_address_line_two)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_line_two', $him_dhl_shipping_address_line_two);
                }

                if (isset($him_dhl_shipping_address_line_three)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_line_three', $him_dhl_shipping_address_line_three);
                }

                if (isset($him_dhl_shipping_address_city)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_city', $him_dhl_shipping_address_city);
                }

                if (isset($him_dhl_shipping_address_state)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_state', $him_dhl_shipping_address_state);
                }

                if (isset($him_dhl_shipping_address_district)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_district', $him_dhl_shipping_address_district);
                }

                if (isset($him_dhl_shipping_address_country)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_country', $him_dhl_shipping_address_country);
                }

                if (isset($him_dhl_shipping_address_postcode)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_postcode', $him_dhl_shipping_address_postcode);
                }

                if (isset($him_dhl_shipping_address_phone)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_phone', $him_dhl_shipping_address_phone);
                }

                if (isset($him_dhl_shipping_address_email)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_email', $him_dhl_shipping_address_email);
                }

                if (isset($him_dhl_shipping_address_return_mode)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_mode', $him_dhl_shipping_address_return_mode);
                }

                //Return new adddress save data

                if (isset($him_dhl_shipping_address_return_company_name)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_company_name', $him_dhl_shipping_address_return_company_name);
                }

                if (isset($him_dhl_shipping_address_return_buyer_name)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_buyer_name', $him_dhl_shipping_address_return_buyer_name);
                }

                if (isset($him_dhl_shipping_address_return_address_one)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_address_one', $him_dhl_shipping_address_return_address_one);
                }

                if (isset($him_dhl_shipping_address_return_address_two)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_address_two', $him_dhl_shipping_address_return_address_two);
                }

                if (isset($him_dhl_shipping_address_return_address_three)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_address_three', $him_dhl_shipping_address_return_address_three);
                }

                if (isset($him_dhl_shipping_address_return_city)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_city', $him_dhl_shipping_address_return_city);
                }

                if (isset($him_dhl_shipping_address_return_state)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_state', $him_dhl_shipping_address_return_state);
                }

                if (isset($him_dhl_shipping_address_return_district)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_district', $him_dhl_shipping_address_return_district);
                }

                if (isset($him_dhl_shipping_address_return_country)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_country', $him_dhl_shipping_address_return_country);
                }

                if (isset($him_dhl_shipping_address_return_postcode)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_postcode', $him_dhl_shipping_address_return_postcode);
                }

                if (isset($him_dhl_shipping_address_return_phone)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_phone', $him_dhl_shipping_address_return_phone);
                }

                if (isset($him_dhl_shipping_address_return_email)) {

                    update_post_meta($post_id, 'him_dhl_shipping_address_return_email', $him_dhl_shipping_address_return_email);
                }

                if (isset($him_dhl_shipping_cash_on_delivery)) {

                    update_post_meta($post_id, 'him_dhl_shipping_cash_on_delivery', $him_dhl_shipping_cash_on_delivery);
                }

                if (isset($him_dhl_shipping_shipment_value_protection)) {

                    update_post_meta($post_id, 'him_dhl_shipping_shipment_value_protection', $him_dhl_shipping_shipment_value_protection);
                }

                if (isset($him_dhl_shipping_shipment_value_ppod)) {

                    update_post_meta($post_id, 'him_dhl_shipping_shipment_value_ppod', $him_dhl_shipping_shipment_value_ppod);
                }

                if (isset($him_dhl_shipping_open_box)) {

                    update_post_meta($post_id, 'him_dhl_shipping_open_box', $him_dhl_shipping_open_box);
                }

                if (isset($him_dhl_shipping_multi_pieces_shipment)) {

                    update_post_meta($post_id, 'him_dhl_shipping_multi_pieces_shipment', $him_dhl_shipping_multi_pieces_shipment);
                }

                $him_dhl_shipping_multi_pieces_shipment = get_post_meta($post_id, 'him_dhl_shipping_multi_pieces_shipment', true);

                if ($him_dhl_shipping_multi_pieces_shipment == 'true') {

                    $isMpsarray = array();

                    //isMpsEdit option
                    update_post_meta($post_id, 'him_dhl_order_ismpsedit_true_option', 'Y');

                    //Repeated meta save code
                    $him_dhl_shipping_single_repeter_group = get_post_meta($post_id, 'him_dhl_shipping_single_repeter_group', true);

                    $him_dhl_piecedescriptionn                      = dhl_him_sanitize_array_recursive($_POST['him_dhl_piecedescription']);
                    $him_dhl_shipment_weightt                       = dhl_him_sanitize_array_recursive($_POST['him_dhl_shipment_weight']);
                    $him_dhl_shipment_billing_ref1                  = dhl_him_sanitize_array_recursive($_POST['him_dhl_shipment_billing_ref_1']);
                    $him_dhl_shipment_billing_ref2                  = dhl_him_sanitize_array_recursive($_POST['him_dhl_shipment_billing_ref_2']);
                    $him_dhl_shipment_billing_shipmentinsurance     = dhl_him_sanitize_array_recursive($_POST['him_dhl_shipment_billing_shipment_insurance']);
                    $him_dhl_shipment_billing_shipment_cash_ondel   = dhl_him_sanitize_array_recursive($_POST['him_dhl_shipment_billing_shipment_cash_on_del']);
                    $count = count($him_dhl_piecedescriptionn);
                    for ($i = 0; $i < $count; $i++) {

                        if ($him_dhl_piecedescriptionn[$i] != '') {
                            $isMpsarray[$i]['him_dhl_piecedescription'] = stripslashes(strip_tags($him_dhl_piecedescriptionn[$i]));
                        }

                        if ($him_dhl_shipment_weightt[$i] != '') {
                            $isMpsarray[$i]['him_dhl_shipment_weight'] = stripslashes($him_dhl_shipment_weightt[$i]);
                        }

                        if ($him_dhl_shipment_billing_ref1[$i] != '') {
                            $isMpsarray[$i]['him_dhl_shipment_billing_ref_1'] = stripslashes($him_dhl_shipment_billing_ref1[$i]);
                        }

                        if ($him_dhl_shipment_billing_ref2[$i] != '') {
                            $isMpsarray[$i]['him_dhl_shipment_billing_ref_2'] = stripslashes($him_dhl_shipment_billing_ref2[$i]);
                        }

                        if ($him_dhl_shipment_billing_shipmentinsurance[$i] != '') {
                            $isMpsarray[$i]['him_dhl_shipment_billing_shipment_insurance'] = stripslashes($him_dhl_shipment_billing_shipmentinsurance[$i]);
                        }

                        if ($him_dhl_shipment_billing_shipment_cash_ondel[$i] != '') {
                            $isMpsarray[$i]['him_dhl_shipment_billing_shipment_cash_on_del'] = stripslashes($him_dhl_shipment_billing_shipment_cash_ondel[$i]);
                        }
                    }

                    if (!empty($isMpsarray) && $isMpsarray != $him_dhl_shipping_single_repeter_group) {
                        update_post_meta($post_id, 'him_dhl_shipping_single_repeter_group', $isMpsarray);
                    } elseif (empty($isMpsarray) && $him_dhl_shipping_single_repeter_group) {
                        delete_post_meta($post_id, 'him_dhl_shipping_single_repeter_group', $him_dhl_shipping_single_repeter_group);
                    }

                    //End Repeater meta save code
                    $him_dhl_shipping_multi_pieces_complete_del = sanitize_text_field($_POST['him_dhl_shipping_multi_pieces_complete_del']);

                    if (isset($him_dhl_shipping_multi_pieces_complete_del)) {
                        update_post_meta($post_id, 'him_dhl_shipping_multi_pieces_complete_del', $him_dhl_shipping_multi_pieces_complete_del);
                    }

                } else {
                    $him_dhl_shipping_multi_pieces_complete_del = sanitize_text_field($_POST['him_dhl_shipping_multi_pieces_complete_del']);
                    delete_post_meta($post_id, 'him_dhl_shipping_multi_pieces_complete_del', $him_dhl_shipping_multi_pieces_complete_del);
                    delete_post_meta($post_id, 'him_dhl_shipping_single_repeter_group', $him_dhl_shipping_single_repeter_group);
                }

                //Weight data save
                $him_dhl_shipping_weight = sanitize_text_field($_POST['him_dhl_shipping_weight']);
                if (isset($him_dhl_shipping_weight)) {

                    update_post_meta($post_id, 'him_dhl_shipping_weight', $him_dhl_shipping_weight);
                }

            }

        }

    }
    new DHL_APAC_CreateShippingLabel();
}