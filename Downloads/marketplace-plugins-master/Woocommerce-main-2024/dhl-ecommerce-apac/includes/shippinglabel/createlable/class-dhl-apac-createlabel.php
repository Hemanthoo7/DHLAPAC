<?php
/**
 * DHL_APAC_Order_Page_CreateShippingLabel setup
 *
 * @package Hyperlink Infosystem DHL Ecommerce APAC
 * @since   1.0
 */


/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {exit;}


/**
 * Hyperlink Infosystem DHL Ecommerce APAC Create Lable Class.
 *
 * @class DHL_APAC_Order_Page_CreateShippingLabel
 */

if (!class_exists('DHL_APAC_Order_Page_CreateShippingLabel')) {

    class DHL_APAC_Order_Page_CreateShippingLabel
    {

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC Order for label.
         *
         * @var PR_DHL_Logger
         */
        protected $logger = null;

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC Create Lable constructor.
         */
        public function __construct()
        {

            add_action('template_redirect', array($this, 'him_dhl_order_page_id'));
            add_action('add_meta_boxes', array($this, 'him_dhl_order_create_lable_meta_box'));
            add_action('woocommerce_process_shop_order_meta', array($this, 'him_dhl_create_lable'), 20);
        }

        public function him_dhl_order_log_msg( $msg ) {

            try {

                $dhl_debug = 'yes';

                if( ! $this->logger ) {
                    $this->logger = new DHL_APAC_Order_Page_CreateLog( $dhl_debug );
                }

                $this->logger->write( $msg );

            } catch (Exception $e) {
                // do nothing
            }
        }

        /**
         * DHL Malaysia Form..
         *
         * @return Order page ID
         */
        public function him_dhl_order_page_id()
        {

            $order_ID = get_the_ID();

            return $order_ID;

        }

        /**
         * DHL Malaysia Form..
         *
         * @return Order shipping details
         */
        public function him_dhl_order_shipping_details_return()
        {

            $Him_shipping_data_return = array();

            $him_dhl_order_page_id = $this->him_dhl_order_page_id();
            $himorderID = new WC_Order($him_dhl_order_page_id);

            if (!empty($himorderID)) {

                $Him_shipping_data_return['get_shipping_company']   = $himorderID->shipping_company;
                $Him_shipping_data_return['get_shipping_name']      = $himorderID->shipping_first_name . ' ' . $himorderID->shipping_last_name;
                $Him_shipping_data_return['get_shipping_city']      = $himorderID->shipping_city;
                
                // Get full name of the state
                $state_code = $himorderID->shipping_state;
                $full_state_name = $this->get_full_state_name($state_code);
                $Him_shipping_data_return['get_shipping_state']     = $full_state_name;

                $Him_shipping_data_return['get_shipping_country']   = $himorderID->shipping_country;
                $Him_shipping_data_return['get_shipping_postcode']  = $himorderID->shipping_postcode;
                $Him_shipping_data_return['get_billing_phone']      = $himorderID->billing_phone;
                $Him_shipping_data_return['get_billing_email']      = $himorderID->billing_email;

                $Him_shipping_data_return['get_shipping_address']   = $himorderID->shipping_address_1;
                $Him_shipping_data_return['get_shipping_address2']   = $himorderID->shipping_address_2;

                $Him_shipping_data_return['get_billing_company']   = $himorderID->billing_company;
                $Him_shipping_data_return['get_billing_name']      = $himorderID->billing_first_name . ' ' . $himorderID->billing_last_name;
                $Him_shipping_data_return['get_billing_city']      = $himorderID->billing_city;
                $Him_shipping_data_return['get_billing_state']     = $himorderID->billing_state;
                $Him_shipping_data_return['get_billing_country']   = $himorderID->billing_country;
                $Him_shipping_data_return['get_billing_postcode']  = $himorderID->billing_postcode;
                $Him_shipping_data_return['get_billing_address']   = $himorderID->billing_address_1;
                $Him_shipping_data_return['get_billing_address2']  = $himorderID->billing_address_2;

            }

            return $Him_shipping_data_return;
        }

        // Function to convert state code to full state name
        private function get_full_state_name($state_code)
        {
            $state_mappings = array(
                'JHR' => 'Johor',
                'KDH' => 'Kedah',
                'KTN' => 'Kelantan',
                'MLK' => 'Melaka',
                'NSN' => 'Negeri Sembilan',
                'PHG' => 'Pahang',
                'PNG' => 'Penang',
                'PRK' => 'Perak',
                'PLS' => 'Perlis',
                'SBH' => 'Sabah',
                'SWK' => 'Sarawak',
                'SGR' => 'Selangor',
                'TRG' => 'Terengganu',
                'KUL' => 'Kuala Lumpur',
                'PJY' => 'Putrajaya',
                'LBN' => 'Labuan',
                // Add more state mappings as needed
            );

            if (isset($state_mappings[$state_code])) {
                return $state_mappings[$state_code];
            } else {
                return $state_code; // Return state code if full name is not found
            }
        }

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC Add the meta box for create lable info on the order page
         *
         */
        public function him_dhl_order_create_lable_meta_box()
        {
            add_meta_box('him-woocommerce-create-dhl-label', sprintf(__('DHL Label', 'dhl-ecommerce-apac'), 'dhl-ecommerce-apac'), array($this, 'him_dhl_show_lable'), 'shop_order', 'side', 'default');
        }

        /**
         * call api for create shipment label
         *
         */
        public function him_dhl_create_lable($post_id)
        {

            $dhl_him_create_lable = sanitize_text_field( $_POST['dhl_him_create_lable'] );

            if (isset($dhl_him_create_lable) && !empty($dhl_him_create_lable)) {

                $errorarray = array();

                $shippingarray = array();

                $get_current_user_login_id = get_current_user_id();
                $date_default_timezone_get = date_default_timezone_get();

                $him_dhl_order_page_id = $post_id;
                $him_dhl_order_lable_form_data_save     = get_post_meta($him_dhl_order_page_id, 'him_dhl_order_lable_form_data_save', true);
                $accessToken                            = get_user_meta($get_current_user_login_id, 'dhlecommerce_auth_api_token', true);
                $him_dhl_shipping_handover_method       = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_handover_method', true);
                $him_dhl_shipping_pickup_date           = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_pickup_date', true);
                $datetime                               = new \DateTime($him_dhl_shipping_pickup_date, new \DateTimeZone($date_default_timezone_get));
                $pickupDateTime                         = $datetime->format('c');

                //Get pickupAddress fields
                $him_dhl_shipping_companyName           = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_companyName', true);
                $him_dhl_shipping_buyer_name            = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_buyer_name', true);
                $him_dhl_shipping_address_line_one      = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_line_one', true);
                $him_dhl_shipping_address_line_two      = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_line_two', true);
                $him_dhl_shipping_address_line_three    = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_line_three', true);
                $him_dhl_shipping_address_city          = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_city', true);
                $him_dhl_shipping_address_state         = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_state', true);
                $him_dhl_shipping_address_country       = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_country', true);
                if ($him_dhl_shipping_address_country == 'malaysia') {
                    $him_dhl_shipping_address_country = 'MY';
                } else {
                    $him_dhl_shipping_address_country = $him_dhl_shipping_address_country;
                }
                $him_dhl_shipping_address_postcode      = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_postcode', true);
                $him_dhl_shipping_address_phone         = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_phone', true);
                $him_dhl_shipping_address_email         = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_email', true);

                //Shipping Data return function
                $him_dhl_return_shipping_data = $this->him_dhl_order_shipping_details_return();

                //Shipping Name & Billing Name white space remove
                if( empty(trim($him_dhl_return_shipping_data['get_shipping_name']))) {
                    $shippingarray['get_billing_or_shipping_name'] = $him_dhl_return_shipping_data['get_billing_name'];
                } else {
                    $shippingarray['get_billing_or_shipping_name'] = $him_dhl_return_shipping_data['get_shipping_name'];
                }

                //Get returnAddress fields
                $him_dhl_shipping_address_return_company_name   = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_company_name', true);
                $him_dhl_shipping_address_return_buyer_name     = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_buyer_name', true);
                $him_dhl_shipping_address_return_address_one    = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_address_one', true);
                $him_dhl_shipping_address_return_address_two    = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_address_two', true);
                $him_dhl_shipping_address_return_address_three  = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_address_three', true);
                $him_dhl_shipping_address_return_city           = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_city', true);
                $him_dhl_shipping_address_return_state          = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_state', true);
                $him_dhl_shipping_address_return_country        = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_country', true);
                if ($him_dhl_shipping_address_return_country == 'malaysia') {
                    $him_dhl_shipping_address_return_country = 'MY';
                } else {
                    $him_dhl_shipping_address_return_country = $$him_dhl_shipping_address_return_country;
                }
                $him_dhl_shipping_address_return_postcode       = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_postcode', true);
                $him_dhl_shipping_address_return_phone          = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_phone', true);
                $him_dhl_shipping_address_return_email          = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_email', true);

                //Get Shipment ID
                $him_dhl_order_shipment_id                  = get_post_meta($him_dhl_order_page_id, 'him_dhl_order_shipment_id', true);
                //$him_dhl_order_shipment_id                            = 'TESQDHL4869';

                //Get Address Return Mode
                $him_dhl_shipping_address_return_mode       = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_mode', true);

                //Get Package description
                $him_dhl_shipping_package_description       = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_package_description', true);

                //Get Shipment Weight
                $him_dhl_shipping_weight                    = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_weight', true);

                //Get product code
                $him_dhl_order_product_code                 = get_post_meta($him_dhl_order_page_id, 'him_dhl_order_product_code', true);

                //Get cash on delievry data
                $him_dhl_shipping_cash_on_delivery          = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_cash_on_delivery', true);

                //Get insuranceValue
                $him_dhl_shipping_shipment_value_protection = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_shipment_value_protection', true);

                //Get currency
                $him_dhl_shipping_currency                  = get_option('woocommerce_currency');

                //Get remark field
                $him_dhl_shipping_remark                    = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_remark', true);

                //Get isMulti true or false
                $him_dhl_shipping_multi_pieces_shipment     = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_multi_pieces_shipment', true);

                if (!empty($him_dhl_shipping_multi_pieces_shipment)) {
                    $him_dhl_shipping_multi_pieces_shipment = 'true';
                } else {
                    $him_dhl_shipping_multi_pieces_shipment = 'false';
                }

                //Get delievry option
                $him_dhl_shipping_multi_pieces_complete_del = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_multi_pieces_complete_del', true);
                $him_dhl_shipping_single_repeter_group      = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_single_repeter_group', true);

                //Get lable template and format
                $dhl_lable_template = get_user_meta($get_current_user_login_id, 'dhl_lable_template', true);
                $dhl_lable_format   = get_user_meta($get_current_user_login_id, 'dhl_lable_format', true);

                //Base64 image meta field
                $him_dhl_shipping_lable_image_content   = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_image_content', true);
                $him_dhl_shipping_lable_delivery_number = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_delivery_number', true);

                $him_dhl_get_edit_shipping_lable    = sanitize_text_field( $_GET['editlable'] );
                $him_dhl_get_delete_shipping_lable  = sanitize_text_field( $_GET['deletelable'] );

                if ($him_dhl_get_edit_shipping_lable == 'true') {
                    update_post_meta($him_dhl_order_page_id, 'him_dhl_edit_shipping_lable', 1);
                } elseif ($him_dhl_get_delete_shipping_lable == 'true') {
                    update_post_meta($him_dhl_order_page_id, 'him_dhl_edit_shipping_lable', 2);
                }

                $him_dhl_edit_shipping_lable = get_post_meta($him_dhl_order_page_id, 'him_dhl_edit_shipping_lable', true);

                // Get pickupAccountId and soldToAccountId
                $dhl_pickup_account         = get_user_meta($get_current_user_login_id, 'dhl_pickup_account', true);
                $him_pickup_account_field   = get_post_meta($dhl_pickup_account, '_him_pickup_account_field', true);
                $dhl_soldto_account         = get_user_meta($get_current_user_login_id, 'dhl_soldto_account', true);

                //isMpsEdit option filed data
                $him_dhl_order_ismpsedit_true_option = get_post_meta($him_dhl_order_page_id, 'him_dhl_order_ismpsedit_true_option', true);

                //District Fields
                $him_dhl_shipping_address_district          = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_district', true);
                $him_dhl_shipping_address_return_district   = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_district', true);

                $him_dhl_shipping_address_district          = ($him_dhl_shipping_address_district) ? $him_dhl_shipping_address_district : null;
                $him_dhl_shipping_address_return_district   = ($him_dhl_shipping_address_return_district) ? $him_dhl_shipping_address_return_district : null;

                $him_dhl_shipping_cash_on_delivery          = (isset($him_dhl_shipping_cash_on_delivery) && ($him_dhl_shipping_cash_on_delivery > 0)) ? floatval($him_dhl_shipping_cash_on_delivery) : null;

                $him_dhl_shipping_shipment_value_protection = (isset($him_dhl_shipping_shipment_value_protection) && ($him_dhl_shipping_shipment_value_protection > 0)) ? floatval($him_dhl_shipping_shipment_value_protection) : null;

                // Open Box Fields
                $him_dhl_shipping_open_box              = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_open_box', true);
                $him_dhl_shipping_shipment_value_ppod   = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_shipment_value_ppod', true);

                // if ( $him_dhl_order_lable_form_data_save == 'yes' ) {

                if ($dhl_him_create_lable == 'Update Label') {

                    // if ( $him_dhl_get_edit_shipping_lable !== 'true' ) {

                    delete_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_error_content');

                    $tz = date_default_timezone_get();

                    $date = new \DateTime('NOW');
                    $messageDateTime = $date->format('c');

                    $headers = array(
                        'Content-Type' => 'application/json',
                    );

                    $postArray = [

                        'labelRequest' => [

                            'hdr' => [
                                'messageType' => 'EDITSHIPMENT',
                                'messageDateTime' => $messageDateTime,
                                'accessToken' => $accessToken,
                                'messageVersion' => '1.4',
                                'messageLanguage' => 'en',
                                'messageSource'   => 'WC',
                            ],
                            'bd' => [

                                'inlineLabelReturn' => 'Y',
                                'customerAccountId' => null,
                                'pickupAccountId' => $him_pickup_account_field,
                                'soldToAccountId' => $dhl_soldto_account,
                                'handoverMethod' => (int) $him_dhl_shipping_handover_method,
                                'pickupDateTime' => $pickupDateTime,
                                'pickupAddress' => [
                                    'companyName' => $him_dhl_shipping_companyName,
                                    'name' => $him_dhl_shipping_buyer_name,
                                    'address1' => (isset($him_dhl_shipping_address_line_one)) ? $him_dhl_shipping_address_line_one : null,
                                    'address2' => (isset($him_dhl_shipping_address_line_two)) ? $him_dhl_shipping_address_line_two : null,
                                    'address3' => (isset($him_dhl_shipping_address_line_three)) ? $him_dhl_shipping_address_line_three : null,
                                    'city' => $him_dhl_shipping_address_city,
                                    'state' => $him_dhl_shipping_address_state,
                                    'district' => null,
                                    'country' => $him_dhl_shipping_address_country,
                                    'postCode' => $him_dhl_shipping_address_postcode,
                                    'phone' => $him_dhl_shipping_address_phone,
                                    'email' => $him_dhl_shipping_address_email,
                                ],
                                'shipperAddress' => [
                                    'companyName' => $him_dhl_shipping_companyName,
                                    'name' => $him_dhl_shipping_buyer_name,
                                    'address1' => (isset($him_dhl_shipping_address_line_one)) ? $him_dhl_shipping_address_line_one : null,
                                    'address2' => (isset($him_dhl_shipping_address_line_two)) ? $him_dhl_shipping_address_line_two : null,
                                    'address3' => (isset($him_dhl_shipping_address_line_three)) ? $him_dhl_shipping_address_line_three : null,
                                    'city' => $him_dhl_shipping_address_city,
                                    'state' => $him_dhl_shipping_address_state,
                                    'district' => $him_dhl_shipping_address_district,
                                    'country' => $him_dhl_shipping_address_country,
                                    'postCode' => $him_dhl_shipping_address_postcode,
                                    'phone' => $him_dhl_shipping_address_phone,
                                    'email' => $him_dhl_shipping_address_email,
                                ],
                                'shipmentItems' => [
                                    '0' => [
                                        'consigneeAddress' => [
                                            'companyName' => (
                                                $him_dhl_return_shipping_data['get_shipping_company'] ? $him_dhl_return_shipping_data['get_shipping_company'] : ($him_dhl_return_shipping_data['get_billing_company'] ? $him_dhl_return_shipping_data['get_billing_company'] : null)
                                            ),
                                            'name' => ($shippingarray['get_billing_or_shipping_name']) ? $shippingarray['get_billing_or_shipping_name'] : null,
                                            'address1' => (
                                                $him_dhl_return_shipping_data['get_shipping_address'] ? $him_dhl_return_shipping_data['get_shipping_address'] : ($him_dhl_return_shipping_data['get_billing_address'] ? $him_dhl_return_shipping_data['get_billing_address'] : null)
                                            ),
                                            'address2' => (
                                                $him_dhl_return_shipping_data['get_shipping_address2'] ? $him_dhl_return_shipping_data['get_shipping_address2'] : ($him_dhl_return_shipping_data['get_billing_address2'] ? $him_dhl_return_shipping_data['get_billing_address2'] : null)
                                            ),
                                            'address3' => null,
                                            'city' => (
                                                $him_dhl_return_shipping_data['get_shipping_city'] ? $him_dhl_return_shipping_data['get_shipping_city'] : ($him_dhl_return_shipping_data['get_billing_city'] ? $him_dhl_return_shipping_data['get_billing_city'] : null)
                                            ),
                                            'state' => (
                                                $him_dhl_return_shipping_data['get_shipping_state'] ? $him_dhl_return_shipping_data['get_shipping_state'] : ($him_dhl_return_shipping_data['get_billing_state'] ? $him_dhl_return_shipping_data['get_billing_state'] : null)
                                            ),
                                            'district' => null,
                                            'country' => (
                                                $him_dhl_return_shipping_data['get_shipping_country'] ? $him_dhl_return_shipping_data['get_shipping_country'] : ($him_dhl_return_shipping_data['get_billing_country'] ? $him_dhl_return_shipping_data['get_billing_country'] : null)
                                            ),
                                            'postCode' => (
                                                $him_dhl_return_shipping_data['get_shipping_postcode'] ? $him_dhl_return_shipping_data['get_shipping_postcode'] : ($him_dhl_return_shipping_data['get_billing_postcode'] ? $him_dhl_return_shipping_data['get_billing_postcode'] : null)
                                            ),
                                            'phone' => ($him_dhl_return_shipping_data['get_billing_phone']) ? $him_dhl_return_shipping_data['get_billing_phone'] : null,
                                            'email' => ($him_dhl_return_shipping_data['get_billing_email']) ? $him_dhl_return_shipping_data['get_billing_email'] : null,
                                            'idNumber' => null,
                                            'idType' => null,
                                        ],
                                        'returnAddress' => [
                                            'companyName' => ($him_dhl_shipping_address_return_company_name) ? $him_dhl_shipping_address_return_company_name : $him_dhl_shipping_companyName,
                                            'name' => ($him_dhl_shipping_address_return_buyer_name) ? $him_dhl_shipping_address_return_buyer_name : $him_dhl_shipping_buyer_name,
                                            'address1' => ($him_dhl_shipping_address_return_address_one) ? $him_dhl_shipping_address_return_address_one : $him_dhl_shipping_address_line_one,
                                            'address2' => ($him_dhl_shipping_address_return_address_two) ? $him_dhl_shipping_address_return_address_two : $him_dhl_shipping_address_line_two,
                                            'address3' => ($him_dhl_shipping_address_return_address_three) ? $him_dhl_shipping_address_return_address_three : $him_dhl_shipping_address_line_three,
                                            'city' => ($him_dhl_shipping_address_return_city) ? $him_dhl_shipping_address_return_city : $him_dhl_shipping_address_city,
                                            'state' => ($him_dhl_shipping_address_return_state) ? $him_dhl_shipping_address_return_state : $him_dhl_shipping_address_state,
                                            'district' => ($him_dhl_shipping_address_return_district) ? $him_dhl_shipping_address_return_district : null,
                                            'country' => ($him_dhl_shipping_address_return_country) ? $him_dhl_shipping_address_return_country : $him_dhl_shipping_address_country,
                                            'postCode' => ($him_dhl_shipping_address_return_postcode) ? $him_dhl_shipping_address_return_postcode : $him_dhl_shipping_address_postcode,
                                            'phone' => ($him_dhl_shipping_address_return_phone) ? $him_dhl_shipping_address_return_phone : $him_dhl_shipping_address_phone,
                                            'email' => ($him_dhl_shipping_address_return_email) ? $him_dhl_shipping_address_return_email : $him_dhl_shipping_address_email,
                                        ],
                                        'shipmentID' => $him_dhl_order_shipment_id,
                                        'returnMode' => $him_dhl_shipping_address_return_mode,
                                        'deliveryConfirmationNo' => null,
                                        'packageDesc' => (isset($him_dhl_shipping_package_description)) ? trim($him_dhl_shipping_package_description) : null,
                                        'totalWeight' => (isset($him_dhl_shipping_weight)) ? (int) $him_dhl_shipping_weight : null,
                                        'totalWeightUOM' => 'G',
                                        'dimensionUOM' => 'cm',
                                        'height' => null,
                                        'length' => null,
                                        'width' => null,
                                        'customerReference1' => null,
                                        'customerReference2' => null,
                                        'productCode' => (isset($him_dhl_order_product_code)) ? $him_dhl_order_product_code : null,
                                        'incoterm' => null,
                                        'contentIndicator' => null,
                                        'codValue' => $him_dhl_shipping_cash_on_delivery,
                                        'insuranceValue' => $him_dhl_shipping_shipment_value_protection,
                                        'freightCharge' => null,
                                        'totalValue' => null,
                                        'currency' => (isset($him_dhl_shipping_currency)) ? $him_dhl_shipping_currency : null,
                                        "remarks" => ($him_dhl_shipping_remark) ? $him_dhl_shipping_remark : null,
                                        "isMult" => $him_dhl_shipping_multi_pieces_shipment,
                                        "isMpsEdit" => ($him_dhl_order_ismpsedit_true_option) ? $him_dhl_order_ismpsedit_true_option : 'N',
                                        "deliveryOption" => ($him_dhl_shipping_multi_pieces_complete_del) ? $him_dhl_shipping_multi_pieces_complete_del : null,
                                    ],
                                ],

                            ],

                        ],

                    ];

                    //IS OpenBox yes
                    if (($him_dhl_shipping_open_box == 'yes') && ($him_dhl_shipping_shipment_value_ppod == 'yes')) {

                        $postArray['labelRequest']['bd']['shipmentItems']['0']['valueAddedServices'] = [

                            'valueAddedService' => [
                                [
                                    'vasCode' => 'OBOX',
                                ],
                                [
                                    'vasCode' => 'PPOD',
                                ],
                            ],

                        ];

                    } elseif ($him_dhl_shipping_open_box == 'yes') {

                        $postArray['labelRequest']['bd']['shipmentItems']['0']['valueAddedServices'] = [

                            'valueAddedService' => [
                                [
                                    'vasCode' => 'OBOX',
                                ],
                            ],

                        ];

                    } elseif ($him_dhl_shipping_shipment_value_ppod == 'yes') {

                        $postArray['labelRequest']['bd']['shipmentItems']['0']['valueAddedServices'] = [

                            'valueAddedService' => [
                                [
                                    'vasCode' => 'PPOD',
                                ],
                            ],

                        ];

                    } else {
                        //Nothing else
                    }

                    //Is multi true
                    if ($him_dhl_shipping_multi_pieces_shipment == 'true') {
                        foreach ($him_dhl_shipping_single_repeter_group as $p => $v) {

                            $postArray['labelRequest']['bd']['shipmentItems']['0']['shipmentPieces'][$p] = [
                                'pieceID' => $p + 1,
                                'announcedWeight' => [
                                    'weight' => (isset($v['him_dhl_shipment_weight'])) ? (int) $v['him_dhl_shipment_weight'] : null,
                                    'unit' => 'G',
                                ],
                                'codAmount' => (isset($v['him_dhl_shipment_billing_shipment_cash_on_del'])) ? floatval($v['him_dhl_shipment_billing_shipment_cash_on_del']) : null,
                                'insuranceAmount' => (isset($v['him_dhl_shipment_billing_shipment_insurance'])) ? floatval($v['him_dhl_shipment_billing_shipment_insurance']) : null,
                                'billingReference1' => (isset($v['him_dhl_shipment_billing_ref_1'])) ? $v['him_dhl_shipment_billing_ref_1'] : null,
                                'billingReference2' => (isset($v['him_dhl_shipment_billing_ref_2'])) ? $v['him_dhl_shipment_billing_ref_2'] : null,
                                'pieceDescription' => (isset($v['him_dhl_piecedescription'])) ? $v['him_dhl_piecedescription'] : null,
                            ];

                        }
                    }
                    //End Is multi true

                    //Get Lable format
                    $postArray['labelRequest']['bd']['label'] = [
                        'pageSize' => '400x600',
                        'format' => $dhl_lable_format,
                        'layout' => $dhl_lable_template,
                    ];

                    $postData = json_encode($postArray);
                    update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_postData', sanitize_text_field($postData));

                    // Response argument passing here
                    $api_args = array(
                        'headers' => $headers,
                        'body'    => $postData,
                        'timeout' => 60,
                        'sslverify' => false, // Change to true if SSL is required
                    );

                    $apiResponse    = wp_remote_post( 'https://api.dhlecommerce.dhl.com/rest/v2/Label/Edit', $api_args );
                    $response_body  = wp_remote_retrieve_body( $apiResponse );

                    $this->him_dhl_order_log_msg($response_body);

                    $responseArray  = json_decode( $response_body, true );
                    $response       = json_encode($responseArray);

                    $resBody = $responseArray['labelResponse']['bd'];
                    $resSts = $resBody['responseStatus'];

                    if ($resSts['code'] == 200) {

                        $himdhldeliveryConfirmationArray = [];
                        $mpsContent = (isset($responseArray['labelResponse']['bd']['labels'][0]['pieces'])) ? $responseArray['labelResponse']['bd']['labels'][0]['pieces'] : '';
                        $ppodLabel = (isset($responseArray['labelResponse']['bd']['labels'][0]['ppodLabel'])) ? $responseArray['labelResponse']['bd']['labels'][0]['ppodLabel'] : '';

                        if (!empty($mpsContent)) {
                            foreach ($mpsContent as $himdhlkey => $piece) {

                                if (!empty($piece['ppodLabel'])) {
                                    $himdhldeliveryConfirmationArray[$him_dhl_order_page_id] = $piece['ppodLabel'];
                                }
                                $himdhldeliveryConfirmationArray[$piece['shipmentPieceID']] = $piece['content'];

                            }
                            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content', $himdhldeliveryConfirmationArray);
                            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content_dhl_lable_format', $dhl_lable_format);
                        } elseif (!empty($ppodLabel)) {

                            $deliveryConfirmationNo = $resBody['labels'][0]['deliveryConfirmationNo'];
                            $base64stringtoimage = $resBody['labels'][0]['content'];

                            if (!empty($deliveryConfirmationNo) && !empty($base64stringtoimage)) {

                                $himdhldeliveryConfirmationArray[$deliveryConfirmationNo] = $base64stringtoimage;
                                $himdhldeliveryConfirmationArray[$him_dhl_order_page_id] = $ppodLabel;

                                update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content', $himdhldeliveryConfirmationArray);
                                update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content_dhl_lable_format', $dhl_lable_format);

                            }
                        } else {

                            $deliveryConfirmationNo = $resBody['labels'][0]['deliveryConfirmationNo'];
                            $base64stringtoimage = $resBody['labels'][0]['content'];

                            if (!empty($deliveryConfirmationNo) && !empty($base64stringtoimage)) {

                                $himdhldeliveryConfirmationArray[$deliveryConfirmationNo] = $base64stringtoimage;

                                update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content', $himdhldeliveryConfirmationArray);
                                update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content_dhl_lable_format', $dhl_lable_format);

                            }

                        }

                    } else {

                        $messageDetails = $resBody['labels'][0]['responseStatus']['messageDetails'][0]['messageDetail'];
                        update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_error_content', $messageDetails);
                    }
                    // }

                } elseif ($dhl_him_create_lable == 'Cancel Label') {
                    $tz = date_default_timezone_get();

                    $date = new \DateTime('NOW');
                    $messageDateTime = $date->format('c');

                    $headers = array(
                        'Content-Type' => 'application/json',
                    );

                    $postArray = [
                        'deleteShipmentReq' => [
                            'hdr' => [
                                'messageType' => 'DELETESHIPMENT',
                                'messageDateTime' => $messageDateTime,
                                'accessToken' => $accessToken,
                                'messageVersion' => '1.4',
                                'messageLanguage' => 'en',
                                'messageSource'   => 'WC',
                            ],
                            'bd' => [
                                'pickupAccountId' => $him_pickup_account_field,
                                'soldToAccountId' => $dhl_soldto_account,
                                'shipmentItems' => [
                                    '0' => [
                                        'shipmentID' => $him_dhl_order_shipment_id,
                                    ],
                                ],
                            ],
                        ],
                    ];

                    $postData = json_encode($postArray);
                    update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_postData', sanitize_text_field($postData));

                    // Response argument passing here
                    $api_args = array(
                        'headers' => $headers,
                        'body'    => $postData,
                        'timeout' => 60,
                        'sslverify' => false, // Change to true if SSL is required
                    );

                    $apiResponse    = wp_remote_post( 'https://api.dhlecommerce.dhl.com/rest/v2/Label/Delete', $api_args );
                    $response_body  = wp_remote_retrieve_body( $apiResponse );

                    $this->him_dhl_order_log_msg($response_body);

                    $responseArray  = json_decode( $response_body, true );
                    $response       = json_encode($responseArray);

                    $resBody        = $responseArray['deleteShipmentResp']['bd'];
                    $resSts         = $resBody['responseStatus'];

                    $messageDetails = $resBody['shipmentItems'][0]['responseStatus']['messageDetails'][0]['messageDetail'];
                    update_post_meta($him_dhl_order_page_id, 'him_dhl_cancel_shipping_lable', $messageDetails);

                } elseif ($dhl_him_create_lable == 'Create Label') {

                    delete_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_error_content');

                    $headers = array(
                        'Content-Type' => 'application/json',
                    );

                    $tz = date_default_timezone_get();

                    $date = new \DateTime('NOW');
                    $messageDateTime = $date->format('c');

                    $postArray = [

                        'labelRequest' => [

                            'hdr' => [
                                'messageType' => 'LABEL',
                                'messageDateTime' => $messageDateTime,
                                'accessToken' => $accessToken,
                                'messageVersion' => '1.4',
                                'messageLanguage' => 'en',
                                'messageSource'   => 'WC',
                            ],
                            'bd' => [

                                'inlineLabelReturn' => 'Y',
                                'customerAccountId' => null,
                                'pickupAccountId' => $him_pickup_account_field,
                                'soldToAccountId' => $dhl_soldto_account,
                                'handoverMethod' => (int) $him_dhl_shipping_handover_method,
                                'pickupDateTime' => $pickupDateTime,
                                'pickupAddress' => [
                                    'companyName' => $him_dhl_shipping_companyName,
                                    'name' => $him_dhl_shipping_buyer_name,
                                    'address1' => (isset($him_dhl_shipping_address_line_one)) ? $him_dhl_shipping_address_line_one : null,
                                    'address2' => (isset($him_dhl_shipping_address_line_two)) ? $him_dhl_shipping_address_line_two : null,
                                    'address3' => (isset($him_dhl_shipping_address_line_three)) ? $him_dhl_shipping_address_line_three : null,
                                    'city' => $him_dhl_shipping_address_city,
                                    'state' => $him_dhl_shipping_address_state,
                                    'district' => null,
                                    'country' => $him_dhl_shipping_address_country,
                                    'postCode' => $him_dhl_shipping_address_postcode,
                                    'phone' => $him_dhl_shipping_address_phone,
                                    'email' => $him_dhl_shipping_address_email,
                                ],
                                'shipperAddress' => [
                                    'companyName' => $him_dhl_shipping_companyName,
                                    'name' => $him_dhl_shipping_buyer_name,
                                    'address1' => (isset($him_dhl_shipping_address_line_one)) ? $him_dhl_shipping_address_line_one : null,
                                    'address2' => (isset($him_dhl_shipping_address_line_two)) ? $him_dhl_shipping_address_line_two : null,
                                    'address3' => (isset($him_dhl_shipping_address_line_three)) ? $him_dhl_shipping_address_line_three : null,
                                    'city' => $him_dhl_shipping_address_city,
                                    'state' => $him_dhl_shipping_address_state,
                                    'district' => $him_dhl_shipping_address_district,
                                    'country' => $him_dhl_shipping_address_country,
                                    'postCode' => $him_dhl_shipping_address_postcode,
                                    'phone' => $him_dhl_shipping_address_phone,
                                    'email' => $him_dhl_shipping_address_email,
                                ],
                                'shipmentItems' => [
                                    '0' => [
                                        'consigneeAddress' => [
                                            'companyName' => (
                                                $him_dhl_return_shipping_data['get_shipping_company'] ? $him_dhl_return_shipping_data['get_shipping_company'] : ($him_dhl_return_shipping_data['get_billing_company'] ? $him_dhl_return_shipping_data['get_billing_company'] : null)
                                            ),
                                            'name' => ($shippingarray['get_billing_or_shipping_name']) ? $shippingarray['get_billing_or_shipping_name'] : null,
                                            'address1' => (
                                                $him_dhl_return_shipping_data['get_shipping_address'] ? $him_dhl_return_shipping_data['get_shipping_address'] : ($him_dhl_return_shipping_data['get_billing_address'] ? $him_dhl_return_shipping_data['get_billing_address'] : null)
                                            ),
                                            'address2' => (
                                                $him_dhl_return_shipping_data['get_shipping_address2'] ? $him_dhl_return_shipping_data['get_shipping_address2'] : ($him_dhl_return_shipping_data['get_billing_address2'] ? $him_dhl_return_shipping_data['get_billing_address2'] : null)
                                            ),
                                            'address3' => null,
                                            'city' => (
                                                $him_dhl_return_shipping_data['get_shipping_city'] ? $him_dhl_return_shipping_data['get_shipping_city'] : ($him_dhl_return_shipping_data['get_billing_city'] ? $him_dhl_return_shipping_data['get_billing_city'] : null)
                                            ),
                                            'state' => (
                                                $him_dhl_return_shipping_data['get_shipping_state'] ? $him_dhl_return_shipping_data['get_shipping_state'] : ($him_dhl_return_shipping_data['get_billing_state'] ? $him_dhl_return_shipping_data['get_billing_state'] : null)
                                            ),
                                            'district' => null,
                                            'country' => (
                                                $him_dhl_return_shipping_data['get_shipping_country'] ? $him_dhl_return_shipping_data['get_shipping_country'] : ($him_dhl_return_shipping_data['get_billing_country'] ? $him_dhl_return_shipping_data['get_billing_country'] : null)
                                            ),
                                            'postCode' => (
                                                $him_dhl_return_shipping_data['get_shipping_postcode'] ? $him_dhl_return_shipping_data['get_shipping_postcode'] : ($him_dhl_return_shipping_data['get_billing_postcode'] ? $him_dhl_return_shipping_data['get_billing_postcode'] : null)
                                            ),
                                            'phone' => ($him_dhl_return_shipping_data['get_billing_phone']) ? $him_dhl_return_shipping_data['get_billing_phone'] : null,
                                            'email' => ($him_dhl_return_shipping_data['get_billing_email']) ? $him_dhl_return_shipping_data['get_billing_email'] : null,
                                            'idNumber' => null,
                                            'idType' => null,
                                        ],
                                        'returnAddress' => [
                                            'companyName' => ($him_dhl_shipping_address_return_company_name) ? $him_dhl_shipping_address_return_company_name : $him_dhl_shipping_companyName,
                                            'name' => ($him_dhl_shipping_address_return_buyer_name) ? $him_dhl_shipping_address_return_buyer_name : $him_dhl_shipping_buyer_name,
                                            'address1' => ($him_dhl_shipping_address_return_address_one) ? $him_dhl_shipping_address_return_address_one : $him_dhl_shipping_address_line_one,
                                            'address2' => ($him_dhl_shipping_address_return_address_two) ? $him_dhl_shipping_address_return_address_two : $him_dhl_shipping_address_line_two,
                                            'address3' => ($him_dhl_shipping_address_return_address_three) ? $him_dhl_shipping_address_return_address_three : $him_dhl_shipping_address_line_three,
                                            'city' => ($him_dhl_shipping_address_return_city) ? $him_dhl_shipping_address_return_city : $him_dhl_shipping_address_city,
                                            'state' => ($him_dhl_shipping_address_return_state) ? $him_dhl_shipping_address_return_state : $him_dhl_shipping_address_state,
                                            'district' => ($him_dhl_shipping_address_return_district) ? $him_dhl_shipping_address_return_district : null,
                                            'country' => ($him_dhl_shipping_address_return_country) ? $him_dhl_shipping_address_return_country : $him_dhl_shipping_address_country,
                                            'postCode' => ($him_dhl_shipping_address_return_postcode) ? $him_dhl_shipping_address_return_postcode : $him_dhl_shipping_address_postcode,
                                            'phone' => ($him_dhl_shipping_address_return_phone) ? $him_dhl_shipping_address_return_phone : $him_dhl_shipping_address_phone,
                                            'email' => ($him_dhl_shipping_address_return_email) ? $him_dhl_shipping_address_return_email : $him_dhl_shipping_address_email,
                                        ],
                                        'shipmentID' => $him_dhl_order_shipment_id,
                                        'returnMode' => $him_dhl_shipping_address_return_mode,
                                        'deliveryConfirmationNo' => null,
                                        'packageDesc' => (isset($him_dhl_shipping_package_description)) ? trim($him_dhl_shipping_package_description) : null,
                                        'totalWeight' => (isset($him_dhl_shipping_weight)) ? (int) $him_dhl_shipping_weight : null,
                                        'totalWeightUOM' => 'G',
                                        'dimensionUOM' => 'cm',
                                        'height' => null,
                                        'length' => null,
                                        'width' => null,
                                        'customerReference1' => null,
                                        'customerReference2' => null,
                                        'productCode' => (isset($him_dhl_order_product_code)) ? $him_dhl_order_product_code : null,
                                        'incoterm' => null,
                                        'contentIndicator' => null,
                                        'codValue' => $him_dhl_shipping_cash_on_delivery,
                                        'insuranceValue' => $him_dhl_shipping_shipment_value_protection,
                                        'freightCharge' => null,
                                        'totalValue' => null,
                                        'currency' => (isset($him_dhl_shipping_currency)) ? $him_dhl_shipping_currency : null,
                                        "remarks" => ($him_dhl_shipping_remark) ? $him_dhl_shipping_remark : null,
                                        "isMult" => $him_dhl_shipping_multi_pieces_shipment,
                                        "deliveryOption" => ($him_dhl_shipping_multi_pieces_complete_del) ? $him_dhl_shipping_multi_pieces_complete_del : null,
                                    ],
                                ],

                            ],

                        ],

                    ];

                    //IS OpenBox yes
                    if (($him_dhl_shipping_open_box == 'yes') && ($him_dhl_shipping_shipment_value_ppod == 'yes')) {

                        $postArray['labelRequest']['bd']['shipmentItems']['0']['valueAddedServices'] = [

                            'valueAddedService' => [
                                [
                                    'vasCode' => 'OBOX',
                                ],
                                [
                                    'vasCode' => 'PPOD',
                                ],
                            ],

                        ];

                    } elseif ($him_dhl_shipping_open_box == 'yes') {

                        $postArray['labelRequest']['bd']['shipmentItems']['0']['valueAddedServices'] = [

                            'valueAddedService' => [
                                [
                                    'vasCode' => 'OBOX',
                                ],
                            ],

                        ];

                    } elseif ($him_dhl_shipping_shipment_value_ppod == 'yes') {

                        $postArray['labelRequest']['bd']['shipmentItems']['0']['valueAddedServices'] = [

                            'valueAddedService' => [
                                [
                                    'vasCode' => 'PPOD',
                                ],
                            ],

                        ];

                    } else {
                        //Nothing else
                    }

                    //Is multi true
                    if ($him_dhl_shipping_multi_pieces_shipment == 'true') {
                        foreach ($him_dhl_shipping_single_repeter_group as $p => $v) {

                            $postArray['labelRequest']['bd']['shipmentItems']['0']['shipmentPieces'][$p] = [
                                'pieceID' => $p + 1,
                                'announcedWeight' => [
                                    'weight' => (isset($v['him_dhl_shipment_weight'])) ? (int) $v['him_dhl_shipment_weight'] : null,
                                    'unit' => 'G',
                                ],
                                'codAmount' => (isset($v['him_dhl_shipment_billing_shipment_cash_on_del'])) ? floatval($v['him_dhl_shipment_billing_shipment_cash_on_del']) : null,
                                'insuranceAmount' => (isset($v['him_dhl_shipment_billing_shipment_insurance'])) ? floatval($v['him_dhl_shipment_billing_shipment_insurance']) : null,
                                'billingReference1' => (isset($v['him_dhl_shipment_billing_ref_1'])) ? $v['him_dhl_shipment_billing_ref_1'] : null,
                                'billingReference2' => (isset($v['him_dhl_shipment_billing_ref_2'])) ? $v['him_dhl_shipment_billing_ref_2'] : null,
                                'pieceDescription' => (isset($v['him_dhl_piecedescription'])) ? $v['him_dhl_piecedescription'] : null,
                            ];

                        }
                    }
                    //End Is multi true

                    //Get Lable format
                    $postArray['labelRequest']['bd']['label'] = [
                        'pageSize' => '400x600',
                        'format' => $dhl_lable_format,
                        'layout' => $dhl_lable_template,
                    ];

                    $postData = json_encode($postArray);
                    update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_postData', sanitize_text_field($postData));

                    // Response argument passing here
                    $api_args = array(
                        'headers' => $headers,
                        'body'    => $postData,
                        'timeout' => 60,
                        'sslverify' => false, // Change to true if SSL is required
                    );

                    $apiResponse    = wp_remote_post( 'https://api.dhlecommerce.dhl.com/rest/v2/Label', $api_args );
                    $response_body  = wp_remote_retrieve_body( $apiResponse );

                    $this->him_dhl_order_log_msg($response_body);

                    $responseArray  = json_decode( $response_body, true );
                    $response       = json_encode($responseArray);

                    $resBody        = $responseArray['labelResponse']['bd'];
                    $resSts         = $resBody['responseStatus'];

                    if ($resSts['code'] == 200) {

                        $himdhldeliveryConfirmationArray = [];
                        $mpsContent = (isset($responseArray['labelResponse']['bd']['labels'][0]['pieces'])) ? $responseArray['labelResponse']['bd']['labels'][0]['pieces'] : '';
                        $ppodLabel = (isset($responseArray['labelResponse']['bd']['labels'][0]['ppodLabel'])) ? $responseArray['labelResponse']['bd']['labels'][0]['ppodLabel'] : '';

                        if (!empty($mpsContent)) {
                            foreach ($mpsContent as $himdhlkey => $piece) {

                                if (!empty($piece['ppodLabel'])) {
                                    $himdhldeliveryConfirmationArray[$him_dhl_order_page_id] = $piece['ppodLabel'];
                                }
                                $himdhldeliveryConfirmationArray[$piece['shipmentPieceID']] = $piece['content'];

                            }
                            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content', $himdhldeliveryConfirmationArray);
                            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content_dhl_lable_format', $dhl_lable_format);
                        } elseif (!empty($ppodLabel)) {

                            $deliveryConfirmationNo = $resBody['labels'][0]['deliveryConfirmationNo'];
                            $base64stringtoimage = $resBody['labels'][0]['content'];

                            if (!empty($deliveryConfirmationNo) && !empty($base64stringtoimage)) {

                                $himdhldeliveryConfirmationArray[$deliveryConfirmationNo] = $base64stringtoimage;
                                $himdhldeliveryConfirmationArray[$him_dhl_order_page_id] = $ppodLabel;

                                update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content', $himdhldeliveryConfirmationArray);
                                update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content_dhl_lable_format', $dhl_lable_format);

                            }
                        } else {

                            $deliveryConfirmationNo = $resBody['labels'][0]['deliveryConfirmationNo'];
                            $base64stringtoimage = $resBody['labels'][0]['content'];

                            if (!empty($deliveryConfirmationNo) && !empty($base64stringtoimage)) {

                                $himdhldeliveryConfirmationArray[$deliveryConfirmationNo] = $base64stringtoimage;

                                update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content', $himdhldeliveryConfirmationArray);
                                update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content_dhl_lable_format', $dhl_lable_format);

                            }

                        }

                    } else {
                        $messageDetails = $resBody['labels'][0]['responseStatus']['messageDetails'][0]['messageDetail'];
                        update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_error_content', $messageDetails);
                    }

                }
                //}
            }

        }

        /**
         * Show the meta box for shipment info on the order page
         *
         */
        public function him_dhl_show_lable()
        {

            $him_dhl_order_page_id = $this->him_dhl_order_page_id();
            //Base64 image meta field
            $him_dhl_shipping_lable_mps_content                     = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content', true);
            $him_dhl_shipping_lable_mps_content_dhl_lable_format    = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content_dhl_lable_format', true);
            $him_dhl_cancel_shipping_lable                          = get_post_meta($him_dhl_order_page_id, 'him_dhl_cancel_shipping_lable', true);

            if ($him_dhl_cancel_shipping_lable) {
                echo '<p>' . esc_html($him_dhl_cancel_shipping_lable) . '</p>';
            }

            $him_dhl_shipping_lable_error_content = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_error_content', true);

            if (!empty($him_dhl_shipping_lable_error_content) && isset($him_dhl_shipping_lable_error_content)) {
                echo '<p class="him_dhl_lable_error_message">' . esc_html('Error Details : '.$him_dhl_shipping_lable_error_content) . '</p>';
            }

            if (!empty($him_dhl_shipping_lable_mps_content) && isset($him_dhl_shipping_lable_mps_content)) {

                if ($him_dhl_cancel_shipping_lable == 'Shipment cancellation is successful') {
                    echo '<p style="display:none;">' . esc_html($him_dhl_cancel_shipping_lable) . '</p>';
                } else {
                    echo '<p class="dhl-him-action-btn">';
                        //Edit lable action url
                        echo '<a class="button dhl-him-edit-lable-btn" href="javascript:void(0)">'.esc_html( 'Edit Label' ).'</a>';

                        //Delete lable action url
                        echo '<input type="submit" class="button dhl_him_create_lable dhl-him-delete-lable-btn" name="dhl_him_create_lable" value="'.esc_attr('Cancel Label').'">';
                    echo '</p>';
                }

                foreach ($him_dhl_shipping_lable_mps_content as $mpsdeliveryconfirmnumber => $mpsdeliveryimagecontent) {

                    if ($him_dhl_shipping_lable_mps_content_dhl_lable_format == 'png') {
                        if ($mpsdeliveryconfirmnumber == $him_dhl_order_page_id) {
                            echo '<a download="' . esc_attr( $mpsdeliveryconfirmnumber ) . '-PPOD.' . esc_attr( $him_dhl_shipping_lable_mps_content_dhl_lable_format ) . '" href="data:image/png;base64,' . esc_attr( $mpsdeliveryimagecontent ) . '"><img class="him-dhl-lable-image" height="450" width="260" src="data:image/png;base64,' . esc_attr( $mpsdeliveryimagecontent ) . '" alt="'.esc_attr( 'DHL Label', 'dhl-ecommerce-apac' ).'" /></a><br/><br/>';
                        } else {
                            echo '<a download="' . esc_attr( $mpsdeliveryconfirmnumber ) . '.' . esc_attr( $him_dhl_shipping_lable_mps_content_dhl_lable_format ) . '" href="data:image/png;base64,' . esc_attr( $mpsdeliveryimagecontent ) . '"><img class="him-dhl-lable-image" height="450" width="260" src="data:image/png;base64,' . esc_attr( $mpsdeliveryimagecontent ) . '" alt="'.esc_attr( 'DHL Label', 'dhl-ecommerce-apac' ).'" /></a><br/><br/>';
                        }
                    } else {
                        if ($mpsdeliveryconfirmnumber == $him_dhl_order_page_id) {
                            echo '<a download="' . esc_attr( $mpsdeliveryconfirmnumber ) . '-PPOD.' . esc_attr( $him_dhl_shipping_lable_mps_content_dhl_lable_format ) . '" href="data:image/pdf;base64,' . esc_attr( $mpsdeliveryimagecontent ) . '">'.esc_html( 'View PDF' ).'</a><br/><br/>';
                        } else {
                            echo '<a download="' . esc_attr( $mpsdeliveryconfirmnumber ) . '.' . esc_attr( $him_dhl_shipping_lable_mps_content_dhl_lable_format ) . '" href="data:image/pdf;base64,' . esc_attr( $mpsdeliveryimagecontent ) . '">'.esc_html( 'View PDF' ).'</a><br/><br/>';
                        }
                    }

                }

            } else {
                echo '<p style="display:none;">'.esc_html('There is no lable available for this order.').'</p>';
            }

        }

    }
    new DHL_APAC_Order_Page_CreateShippingLabel();
}