<?php
/**
 * Extra Functions
 *
 * @package Hyperlink Infosystem DHL Ecommerce APAC
 * @since   1.0
 */


/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {exit;}

/**
 * Extra Function File Call.
 *
 * @return Check DHL client Auth API Data
 */
if (!function_exists('DHL_APAC_Check_Client_Auth_APi')) {

    function DHL_APAC_Check_Client_Auth_APi($client_id, $client_secret_passworrd)
    {

        $response = [];

        $current_user_id = get_current_user_id();

        $headers = array(
            'Content-Type' => 'application/json',
        );

        $args = array(
            'headers'     => $headers,
            'timeout'     => 60,
            'sslverify'   => false, // Change to true if SSL is required
        );

        $response = wp_remote_get( 'https://api.dhlecommerce.dhl.com/rest/v1/OAuth/AccessToken?clientId=' . $client_id . '&password=' . $client_secret_passworrd . '&returnFormat=json', $args );

        $response_body  = wp_remote_retrieve_body( $response );
        $response       = json_decode( $response_body, true );

        if (!empty($response)) {

            foreach ($response as $value) {

                $responseStatus = (isset($response['accessTokenResponse']['responseStatus']['code'])) ? $response['accessTokenResponse']['responseStatus']['code'] : '';
                $responsetoeken = (isset($response['accessTokenResponse']['token'])) ? $response['accessTokenResponse']['token'] : '';
                if (!empty($responsetoeken)) {

                    update_user_meta($current_user_id, 'dhlecommerce_auth_api_token', $responsetoeken);

                }
                if ($responseStatus == '100000') {
                    $response = '<p class="successfull" style="color: green;">Connected Successfully</p>';
                } elseif ($responseStatus == '100099') {
                    $response = '<p class="unsuccessfull" style="color: red;">Input parameter(s) does not match any client.</p>';
                } else {
                    $response = '<p class="unsuccessfull" style="color: red;">ClientId or password cannot be blank or null.</p>';
                }

            }
        }

        return $response;

    }

}

/**
 * Extra Function File Call.
 *
 * @return Check Get Pickup-account Shipper address ( new client changes - 16/5/2023 )
 */
function him_get_shipper_address_require_changes() {
    global $wpdb;

    $pickupIDarray = array();

    $current_user_id = get_current_user_id();

    $dhl_pickup_account = get_user_meta($current_user_id, 'dhl_pickup_account', true);

    return $dhl_pickup_account;
}

/**
 * Extra Function File Call.
 *
 * @return Check Testing Connection checking ajax
 */
add_action('wp_ajax_him_check_dhl_auth_client_api', 'him_check_dhl_auth_client_api');
if (!function_exists('him_check_dhl_auth_client_api')) {
    function him_check_dhl_auth_client_api()
    {
        $response               = [];
        $status                 = false;

        if( is_admin() ) {

            $status                 = true;
            $dhl_client_id          = sanitize_text_field($_POST['dhl_client_id']);
            $dhl_secret_client_id   = sanitize_text_field($_POST['dhl_secret_client_id']);
            $response_list          = DHL_APAC_Check_Client_Auth_APi($dhl_client_id, $dhl_secret_client_id);

            $response['him_auth_response']      = $response_list;
            $response['dhl_client_id']          = $dhl_client_id;
            $response['dhl_secret_client_id']   = $dhl_secret_client_id;
            $response['status'] = $status;

        }else{

            $response['status'] = $status;

        }
        
        echo wp_json_encode($response);
        wp_die();
    }
}

/**
 * Extra Function File Call.
 *
 * @param $him_dhl_all_shippingorder_id
 * @return DHL_APAC return shipping address details
 */
if (!function_exists('dhl_apac_multipleorder_shipping_details_return')) {
    function dhl_apac_multipleorder_shipping_details_return($him_dhl_all_shippingorder_id)
    {

        $Him_shipping_data_return = array();

        $him_dhl_order_page_id = $him_dhl_all_shippingorder_id;
        $himorderID = new WC_Order($him_dhl_order_page_id);

        if (!empty($himorderID)) {

            $Him_shipping_data_return['get_shipping_company']   = $himorderID->shipping_company;
            $Him_shipping_data_return['get_shipping_name']      = $himorderID->shipping_first_name . ' ' . $himorderID->shipping_last_name;
            $Him_shipping_data_return['get_shipping_city']      = $himorderID->shipping_city;
            
            // Get full name of the state
            $state_code = $himorderID->shipping_state;
            $full_state_name = get_full_state_name($state_code);
            $Him_shipping_data_return['get_shipping_state']     = $full_state_name;

            $Him_shipping_data_return['get_shipping_country']   = $himorderID->shipping_country;
            $Him_shipping_data_return['get_shipping_postcode']  = $himorderID->shipping_postcode;
            $Him_shipping_data_return['get_billing_phone']      = $himorderID->billing_phone;
            $Him_shipping_data_return['get_billing_email']      = $himorderID->billing_email;

            $Him_shipping_data_return['get_shipping_address']   = $himorderID->shipping_address_1;
            $Him_shipping_data_return['get_shipping_address2']  = $himorderID->shipping_address_2;

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
}

// Function to convert state code to full state name
function get_full_state_name($state_code)
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
 * Extra Function File Call.
 *
 * @param $him_dhl_all_order_id
 * @return DHL_APAC return multiple create lable api call
 */
if (!function_exists('dhl_apac_create_multipleorder_bulk_lable')) {
    function dhl_apac_create_multipleorder_bulk_lable($him_dhl_all_order_id)
    {

        $get_current_user_login_id  = get_current_user_id();
        $him_dhl_order_page_id      = $him_dhl_all_order_id;

        update_post_meta($him_dhl_order_page_id, 'him_dhl_order_lable_form_data_save', 'yes');

        // Get pickupAccountId and soldToAccountId
        $dhl_pickup_account                     = get_user_meta($get_current_user_login_id, 'dhl_pickup_account', true);
        $him_pickup_account_field               = get_post_meta($dhl_pickup_account, '_him_pickup_account_field', true);
        $dhl_soldto_account                     = get_user_meta($get_current_user_login_id, 'dhl_soldto_account', true);
        $him_dhl_order_lable_form_data_save     = get_post_meta($him_dhl_order_page_id, 'him_dhl_order_lable_form_data_save', true);
        $accessToken                            = get_user_meta($get_current_user_login_id, 'dhlecommerce_auth_api_token', true);

        $getOrderproductmname   = wc_get_order($him_dhl_order_page_id);
        $getOrderproductdata    = $getOrderproductmname->get_data();

        if ( !empty( $getOrderproductmname ) ) {
            $productnamearray   = array();
            $weightmetaarray    = array();
            foreach ($getOrderproductmname->get_items() as $order_item) {
                $productnamearray[] = $order_item->get_name();
                $weightmetaarray[]  = $order_item->get_product_id();
            }
        }

        if ( !empty( $weightmetaarray ) ) {
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

            // $him_dhl_shipping_weight = array_sum($getallweight);
            // $him_dhl_shipping_weight = $him_dhl_shipping_weight * 1000;

        }

        if (!empty($productnamearray)) {
            $productnamearray = implode(" ", $productnamearray);
            $productnamearray = substr($productnamearray, 0, 50); // Limit to 50 characters
        } else {
            $productnamearray = '';
        }

        //Get pickupAddress fields
        // $him_dhl_shipping_companyName                   = get_user_meta($get_current_user_login_id, 'dhl_account_type', true);
        // $him_dhl_shipping_buyer_name                    = $getOrderproductdata['shipping']['first_name'];
        // $him_dhl_shipping_address_line_one              = $getOrderproductdata['shipping']['address_1'];
        // $him_dhl_shipping_address_line_two              = $getOrderproductdata['shipping']['address_2'];
        // $him_dhl_shipping_address_line_three            = null;
        // $him_dhl_shipping_address_city                  = $getOrderproductdata['shipping']['city'];
        // $him_dhl_shipping_address_state                 = $getOrderproductdata['shipping']['state'];
        // $him_dhl_shipping_address_country               = get_user_meta($get_current_user_login_id, 'dhl_country', true);

        // $him_dhl_shipping_address_postcode              = $getOrderproductdata['shipping']['postcode'];
        // $him_dhl_shipping_address_phone                 = $getOrderproductdata['billing']['phone'];
        // $him_dhl_shipping_address_email                 = $getOrderproductdata['billing']['email'];

        //Get Shipper address ( new changes requirment )
        $pickupID_get_fn  = him_get_shipper_address_require_changes();

        $him_dhl_shipping_buyer_name            = get_post_meta($pickupID_get_fn, '_him_pickup_your_name_field', true);
        $him_dhl_shipping_address_line_one      = get_post_meta($pickupID_get_fn, '_him_address_one_field', true);
        $him_dhl_shipping_address_line_two      = get_post_meta($pickupID_get_fn, '_him_address_two_field', true);
        $him_dhl_shipping_address_line_three    = get_post_meta($pickupID_get_fn, '_him_address_three_field', true);
        $him_dhl_shipping_address_city          = get_post_meta($pickupID_get_fn, '_him_city_field', true);
        $him_dhl_shipping_address_state         = get_post_meta($pickupID_get_fn, '_him_state_field', true);
        $him_dhl_shipping_address_country       = get_user_meta($get_current_user_login_id, 'dhl_country', true);
        $him_dhl_shipping_address_postcode      = get_post_meta($pickupID_get_fn, '_him_postcode_field', true);
        $him_dhl_shipping_address_phone         = get_post_meta($pickupID_get_fn, '_him_phone_field', true);
        $him_dhl_shipping_address_email         = get_post_meta($pickupID_get_fn, '_him_email_field', true);

        $him_dhl_shipping_companyName           = get_the_title($pickupID_get_fn);

        //COD Populated with mulitple order
        //Auto Populated cash on delivery price
        $payment_method     = get_post_meta($him_dhl_order_page_id, '_payment_method', true);
        $cod_settings       = get_option('woocommerce_cod_settings');

        if ($cod_settings['enabled'] === 'yes' && $payment_method == 'cod') {
            $get_total = floatval( $getOrderproductmname->get_total() );
        }else{
            $get_total = null;
        }

        //Shipping Data return function
        $him_dhl_return_shipping_data                   = dhl_apac_multipleorder_shipping_details_return($him_dhl_order_page_id);

        //Shipping Name & Billing Name white space remove
        if( empty(trim($him_dhl_return_shipping_data['get_shipping_name']))) {
            $shippingarray['get_billing_or_shipping_name'] = $him_dhl_return_shipping_data['get_billing_name'];
        } else {
            $shippingarray['get_billing_or_shipping_name'] = $him_dhl_return_shipping_data['get_shipping_name'];
        }

        //Get returnAddress fields
        $him_dhl_shipping_address_return_company_name   = get_user_meta($get_current_user_login_id, 'dhl_account_type', true);
        $him_dhl_shipping_address_return_buyer_name     = $getOrderproductdata['shipping']['first_name'];
        $him_dhl_shipping_address_return_address_one    = $getOrderproductdata['shipping']['address_1'];
        $him_dhl_shipping_address_return_address_two    = $getOrderproductdata['shipping']['address_2'];
        $him_dhl_shipping_address_return_address_three  = 'null';
        $him_dhl_shipping_address_return_city           = $getOrderproductdata['shipping']['city'];
        $him_dhl_shipping_address_return_state          = $getOrderproductdata['shipping']['state'];

        $him_dhl_shipping_address_return_country        = '';
        $him_dhl_shipping_address_return_country        = get_user_meta($get_current_user_login_id, 'dhl_country', true);

        $him_dhl_shipping_address_return_postcode       = $getOrderproductdata['shipping']['postcode'];
        $him_dhl_shipping_address_return_phone          = $getOrderproductdata['billing']['phone'];
        $him_dhl_shipping_address_return_email          = $getOrderproductdata['billing']['email'];

        //Get Shipment ID
        $dhl_prefix                 = get_user_meta($get_current_user_login_id, 'dhl_prefix', true);
        $him_dhl_order_shipment_id  = $dhl_prefix . $him_dhl_order_page_id;

        //Get Address Return Mode
        $him_dhl_shipping_address_return_mode = '01';

        //Get Package description
        $him_dhl_shipping_package_description = $productnamearray;

        //Get product code
        $him_dhl_order_product_code = get_user_meta($get_current_user_login_id, 'dhl_product_code', true);

        //Get currency
        $him_dhl_shipping_currency  = get_option('woocommerce_currency');

        //Get remark field
        $him_dhl_shipping_remark    = $productnamearray;

        //Get lable template and format
        $dhl_lable_template = get_user_meta($get_current_user_login_id, 'dhl_lable_template', true);
        $dhl_lable_format   = get_user_meta($get_current_user_login_id, 'dhl_lable_format', true);

        //Base64 image meta field
        $him_dhl_shipping_lable_image_content   = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_image_content', true);
        $him_dhl_shipping_lable_delivery_number = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_delivery_number', true);

        //Update all metabix fields
        if ($him_dhl_order_lable_form_data_save == 'yes') {

            update_post_meta($him_dhl_order_page_id, 'him_dhl_order_pickup_account', $dhl_pickup_account);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_order_shipment_id', $him_dhl_order_shipment_id);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_order_product_code', $him_dhl_order_product_code);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_remark', $him_dhl_shipping_remark);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_package_description', $productnamearray);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_handover_method', '1');
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_currency', $him_dhl_shipping_currency);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_companyName', $him_dhl_shipping_companyName);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_buyer_name', $him_dhl_shipping_buyer_name);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_line_one', $him_dhl_shipping_address_line_one);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_line_two', $him_dhl_shipping_address_line_two);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_line_three', $him_dhl_shipping_address_line_three);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_city', $him_dhl_shipping_address_city);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_state', $him_dhl_shipping_address_state);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_country', $him_dhl_shipping_address_country);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_postcode', $him_dhl_shipping_address_postcode);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_phone', $him_dhl_shipping_address_phone);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_email', $him_dhl_shipping_address_email);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_mode', $him_dhl_shipping_address_return_mode);

            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_company_name', $him_dhl_shipping_companyName);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_buyer_name', $him_dhl_shipping_buyer_name);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_address_one', $him_dhl_shipping_address_line_one);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_address_two', $him_dhl_shipping_address_line_two);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_address_three', $him_dhl_shipping_address_line_three);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_city', $him_dhl_shipping_address_city);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_state', $him_dhl_shipping_address_state);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_country', $him_dhl_shipping_address_return_country);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_postcode', $him_dhl_shipping_address_postcode);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_phone', $him_dhl_shipping_address_phone);
            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_address_return_email', $him_dhl_shipping_address_email);

            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_weight', $him_dhl_shipping_weight);

            update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_cash_on_delivery', $get_total);
        }

        $him_dhl_shipping_weight = get_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_weight', true);
        $him_dhl_shipping_weight = ($him_dhl_shipping_weight) ? $him_dhl_shipping_weight : '0';

        if ($him_dhl_shipping_address_country == 'malaysia') {
            $him_dhl_shipping_address_country = 'MY';
        } else {
            $him_dhl_shipping_address_country = $him_dhl_shipping_address_country;
        }

        if ($him_dhl_shipping_address_return_country == 'malaysia') {
            $him_dhl_shipping_address_return_country = 'MY';
        } else {
            $him_dhl_shipping_address_return_country = $him_dhl_shipping_address_return_country;
        }

        if ($him_dhl_order_lable_form_data_save == 'yes') {

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
                        'handoverMethod' => 1,
                        'pickupDateTime' => null,
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
                            'district' => null,
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
                                    'companyName' => $him_dhl_shipping_address_return_company_name,
                                    'name' => $him_dhl_shipping_address_return_buyer_name,
                                    'address1' => (isset($him_dhl_shipping_address_return_address_one)) ? $him_dhl_shipping_address_return_address_one : null,
                                    'address2' => (isset($him_dhl_shipping_address_return_address_two)) ? $him_dhl_shipping_address_return_address_two : null,
                                    'address3' => (isset($him_dhl_shipping_address_return_address_three)) ? $him_dhl_shipping_address_return_address_three : null,
                                    'city' => $him_dhl_shipping_address_return_city,
                                    'state' => $him_dhl_shipping_address_return_state,
                                    'district' => null,
                                    'country' => $him_dhl_shipping_address_return_country,
                                    'postCode' => $him_dhl_shipping_address_return_postcode,
                                    'phone' => $him_dhl_shipping_address_return_phone,
                                    'email' => $him_dhl_shipping_address_return_email,
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
                                'codValue' => $get_total,
                                'insuranceValue' => null,
                                'freightCharge' => null,
                                'totalValue' => null,
                                'currency' => (isset($him_dhl_shipping_currency)) ? $him_dhl_shipping_currency : null,
                                "remarks" => (isset($him_dhl_shipping_remark)) ? $him_dhl_shipping_remark : $him_dhl_shipping_remark,
                                "isMult" => 'false',
                            ],
                        ],

                    ],

                ],

            ];

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

            $responseArray  = json_decode( $response_body, true );
            $response       = json_encode($responseArray);

            $resBody        = $responseArray['labelResponse']['bd'];
            $resSts         = $resBody['responseStatus'];

            if ($resSts['code'] == 200) {

                $himdhldeliveryConfirmationArray = [];
                $deliveryConfirmationNo = $resBody['labels'][0]['deliveryConfirmationNo'];
                $base64stringtoimage = $resBody['labels'][0]['content'];

                if (!empty($deliveryConfirmationNo) && !empty($base64stringtoimage)) {

                    $himdhldeliveryConfirmationArray[$deliveryConfirmationNo] = $base64stringtoimage;

                    update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content', $himdhldeliveryConfirmationArray);
                    update_post_meta($him_dhl_order_page_id, 'him_dhl_shipping_lable_mps_content_dhl_lable_format', $dhl_lable_format);

                }

            }

        }

    }
}

/**
 * Extra Function File Call.
 *
 * @return DHL_APAC return already create lable notice
 */
add_action('admin_notices', 'dhl_apac_already_generated_lable_notice');
if (!function_exists('dhl_apac_already_generated_lable_notice')) {
    function dhl_apac_already_generated_lable_notice()
    {   

        if ( isset( $_GET['him_dhl_generated_bulk_lable'] ) ) {

            $him_dhl_generated_bulk_lable = sanitize_text_field( $_GET['him_dhl_generated_bulk_lable'] );

            if (!empty($him_dhl_generated_bulk_lable) && isset($him_dhl_generated_bulk_lable)) {

                $class      = 'notice notice-error';
                $message    = __('Order #' . $him_dhl_generated_bulk_lable . ' : Label already generated', 'dhl-ecommerce-apac');

                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));

            }

        }

    }
}

/**
 * Extra Function File Call.
 *
 * @return DHL APAC download create lable multiple images
 */

add_action('init', 'dhl_apac_download_generated_lable_multiple_images');
if (!function_exists('dhl_apac_download_generated_lable_multiple_images')) {
    function dhl_apac_download_generated_lable_multiple_images()
    {

        $current_user_id                = get_current_user_id();

        if ( isset( $_GET['him_dhl_downloadlable_orderID'] ) ) {

            $him_dhl_downloadlable_orderID  = sanitize_text_field( $_GET['him_dhl_downloadlable_orderID'] );

            if (!empty($him_dhl_downloadlable_orderID)) {

                $dhl_him_multiple_ids_explode = explode(",", $him_dhl_downloadlable_orderID);

                if (!empty($dhl_him_multiple_ids_explode) && is_array($dhl_him_multiple_ids_explode)) {
                    echo '<div class="dhl-multiple-images-div" style="display:none;">';
                        foreach ($dhl_him_multiple_ids_explode as $dhlhimorderID) {

                            $him_dhl_order_lable_form_data_save                   = get_post_meta($dhlhimorderID, 'him_dhl_order_lable_form_data_save', true);
                            $dhl_lable_format                                     = get_user_meta($current_user_id, 'dhl_lable_format', true);
                            $him_dhl_shipping_lable_mps_content                   = get_post_meta($dhlhimorderID, 'him_dhl_shipping_lable_mps_content', true);
                            $him_dhl_shipping_lable_mps_content_dhl_lable_format  = get_post_meta($dhlhimorderID, 'him_dhl_shipping_lable_mps_content_dhl_lable_format', true);
                            $him_dhl_order_shipment_id                            = get_post_meta($dhlhimorderID, 'him_dhl_order_shipment_id', true);

                            if ($him_dhl_order_lable_form_data_save == 'yes') {
                                if (!empty($him_dhl_shipping_lable_mps_content)) {
                                    $him_dhl_shipping_lable_mps_content_count = count($him_dhl_shipping_lable_mps_content);
                                    if (array_key_exists($dhlhimorderID, $him_dhl_shipping_lable_mps_content)) {
                                        $him_dhl_shipping_lable_mps_content_count = count($him_dhl_shipping_lable_mps_content) - 1;
                                    }
                                    foreach ($him_dhl_shipping_lable_mps_content as $mpsdeliveryconfirmnumber => $mpsdeliveryimagecontent) {

                                        if ($him_dhl_shipping_lable_mps_content_dhl_lable_format == 'png') {

                                            if ($him_dhl_shipping_lable_mps_content_count == 1) {
                                                if ($mpsdeliveryconfirmnumber == $dhlhimorderID) {
                                                    echo '<img class="him-dhl-lable-image" data-tracking-number="' . esc_attr($him_dhl_order_shipment_id) . '-PPOD" data-image-type="' . esc_attr($him_dhl_shipping_lable_mps_content_dhl_lable_format) . '"  height="450" width="260" src="data:image/png;base64,' . esc_attr($mpsdeliveryimagecontent) . '" alt="'.esc_attr( 'DHL Label', 'dhl-ecommerce-apac' ).'" />';
                                                } else {
                                                    echo '<img class="him-dhl-lable-image" data-tracking-number="' . esc_attr($him_dhl_order_shipment_id) . '" data-image-type="' . esc_attr($him_dhl_shipping_lable_mps_content_dhl_lable_format) . '" height="450" width="260" src="data:image/png;base64,' . esc_attr($mpsdeliveryimagecontent) . '" alt="'.esc_attr( 'DHL Label', 'dhl-ecommerce-apac' ).'" />';
                                                }
                                            } else {
                                                if ($mpsdeliveryconfirmnumber == $dhlhimorderID) {
                                                    echo '<img class="him-dhl-lable-image" data-tracking-number="' . esc_attr($him_dhl_order_shipment_id) . '-PPOD" data-image-type="' . esc_attr($him_dhl_shipping_lable_mps_content_dhl_lable_format) . '"  height="450" width="260" src="data:image/png;base64,' . esc_attr($mpsdeliveryimagecontent) . '" alt="'.esc_attr( 'DHL Label', 'dhl-ecommerce-apac' ).'" />';
                                                } else {
                                                    echo '<img class="him-dhl-lable-image" data-tracking-number="' . esc_attr($him_dhl_order_shipment_id) . '-' . esc_attr($mpsdeliveryconfirmnumber) . '" data-image-type="' . esc_attr($him_dhl_shipping_lable_mps_content_dhl_lable_format) . '" height="450" width="260" src="data:image/png;base64,' . esc_attr($mpsdeliveryimagecontent) . '" alt="'.esc_attr( 'DHL Label', 'dhl-ecommerce-apac' ).'" />';
                                                }
                                            }
                                        } else {

                                            if ($him_dhl_shipping_lable_mps_content_count == 1) {
                                                if ($mpsdeliveryconfirmnumber == $dhlhimorderID) {
                                                    echo '<img class="him-dhl-lable-image" data-tracking-number="' . esc_attr($him_dhl_order_shipment_id) . '-PPOD" data-image-type="' . esc_attr($him_dhl_shipping_lable_mps_content_dhl_lable_format) . '" height="450" width="260" src="data:image/pdf;base64,' . esc_attr($mpsdeliveryimagecontent) . '" alt="'.esc_attr( 'DHL Label', 'dhl-ecommerce-apac' ).'" />';
                                                } else {
                                                    echo '<img class="him-dhl-lable-image" data-tracking-number="' . esc_attr($him_dhl_order_shipment_id) . '" data-image-type="' . esc_attr($him_dhl_shipping_lable_mps_content_dhl_lable_format) . '" height="450" width="260" src="data:image/pdf;base64,' . esc_attr($mpsdeliveryimagecontent) . '" alt="'.esc_attr( 'DHL Label', 'dhl-ecommerce-apac' ).'" />';
                                                }
                                            } else {
                                                if ($mpsdeliveryconfirmnumber == $dhlhimorderID) {
                                                    echo '<img class="him-dhl-lable-image" data-tracking-number="' . esc_attr($him_dhl_order_shipment_id) . '-PPOD" data-image-type="' . esc_attr($him_dhl_shipping_lable_mps_content_dhl_lable_format) . '" height="450" width="260" src="data:image/pdf;base64,' . esc_attr($mpsdeliveryimagecontent) . '" alt="'.esc_attr( 'DHL Label', 'dhl-ecommerce-apac' ).'" />';
                                                } else {
                                                    echo '<img class="him-dhl-lable-image" data-tracking-number="' . esc_attr($him_dhl_order_shipment_id) . '-' . esc_attr($mpsdeliveryconfirmnumber) . '" data-image-type="' . esc_attr($him_dhl_shipping_lable_mps_content_dhl_lable_format) . '" height="450" width="260" src="data:image/pdf;base64,' . esc_attr($mpsdeliveryimagecontent) . '" alt="'.esc_attr( 'DHL Label', 'dhl-ecommerce-apac' ).'" />';
                                                }
                                            }
                                        }

                                    }

                                }
                            }

                        }
                    echo '</div>';

                }

            }
        }

    }
}

/**
 * Extra Function File Call.
 *
 * @return Dhl APAC pickup account return all id
 */
if (!function_exists('dhl_him_posts_id_array_him_pickup_account')) {
    function dhl_him_posts_id_array_him_pickup_account()
    {

        $posts_id_array_him_pickup_account = array();

        $him_pickup_account_args = array(
            'post_type' => 'him_pickup_account',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );

        $posts_id_list_him_pickup_account = get_posts($him_pickup_account_args);

        if (!empty($posts_id_list_him_pickup_account)) {

            foreach ($posts_id_list_him_pickup_account as $him_dhl_posts_id_list_value) {

                $posts_id_array_him_pickup_account[] = $him_dhl_posts_id_list_value->ID;

            }

        }

        return $posts_id_array_him_pickup_account;

    }
}


/**
 * If you want to sanitize an array that contains nested arrays or multidimensional arrays, you can use a recursive function that applies sanitize_text_field() to each value in the array.
 *
 * @return Dhl APAC sanitize an array
 */
if (!function_exists('dhl_him_sanitize_array_recursive')) {
    function dhl_him_sanitize_array_recursive($array) {

        foreach ($array as &$value) {

            if (is_array($value)) {

                $value = sanitize_array_recursive($value);

            } else {

                $value = sanitize_text_field($value);

            }
        }

        return $array;
    }
}


/**
 * Bulk print label jquery
 *
 * @return Dhl APAC sanitize an array
 */
add_action( 'admin_enqueue_scripts', 'dhl_him_multiple_Shippinglable_download_script' );
if (!function_exists('dhl_him_multiple_Shippinglable_download_script')) {
    function dhl_him_multiple_Shippinglable_download_script() {

        $screen = get_current_screen();

        if ( isset( $_GET['him_dhl_downloadlable_orderID'] ) ) {

            $him_dhl_downloadlable_orderID  = sanitize_text_field( $_GET['him_dhl_downloadlable_orderID'] );

            if (!empty($him_dhl_downloadlable_orderID)) {

                if ( $screen->post_type == 'shop_order' ) {
                    ?>
                        <script>
                        function download(data) {
                            const a = document.createElement("a")
                            a.href = "data:application/zip;base64," + data
                            a.setAttribute("download", "ShippingLabel.zip")
                            a.style.display = "none"
                            a.addEventListener("click", e => e.stopPropagation()) // not relevant for modern browsers
                            document.body.appendChild(a)
                            setTimeout(() => { // setTimeout - not relevant for modern browsers
                                a.click()
                                document.body.removeChild(a)
                            }, 0)
                        }
                        function download_all() {
                            var zip = new JSZip();
                            [...document.getElementsByClassName("him-dhl-lable-image")]
                            .forEach((img, i) => {
                                let name = img.getAttribute("data-tracking-number")
                                let format = img.getAttribute("data-image-type")
                                zip.file(name + "." + format, img.src.replace(/data:.*?;base64,/, ""), {base64: true})
                            })
                            zip.generateAsync({type: "base64"}).then(download)
                        }
                        document.addEventListener('DOMContentLoaded',download_all)
                        </script>
                    <?php
                }

            }

        }
        
    }
}