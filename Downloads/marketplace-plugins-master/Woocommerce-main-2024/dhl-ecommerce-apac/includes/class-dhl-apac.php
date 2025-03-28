<?php
/**
 * DHL_APAC setup
 *
 * @package Hyperlink Infosystem DHL Ecommerce APAC
 * @since   1.0
 */

defined('ABSPATH') || exit;

/**
 * Main DHL_APAC Class.
 *
 * @class DHL_APAC
 */
if (!class_exists('DHL_APAC')) {
    final class DHL_APAC
    {

        /**
         * Plugin version.
         *
         * @var string
         */
        public $version = '1.0';

        /**
         * The single instance of the class.
         *
         * @since 1.0
         * @package Hyperlink Infosystem DHL Ecommerce APAC
         */
        protected static $_instance = null;

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC Constructor.
         */
        public function __construct()
        {
            add_action('plugins_loaded', array($this, 'dhl_apac_load_plugin_textdomain'));
            add_action('init', array($this, 'dhl_apac_load_plugin'), 0);
        }

        /**
         * Hyperlink Infosystem Determine which plugin to load.
         */
        public function dhl_apac_load_plugin()
        {
            // Checks if WooCommerce is installed.
            if (class_exists('WC_Shipping_Method')) {

                $this->dhl_apac_define_constants();
                $this->dhl_apac_includes();
                $this->plugin_init_set_phpversion();

                add_action('admin_enqueue_scripts', array($this, 'dhl_apac_front_style_js_includes'));
                add_action('admin_menu', array($this, 'dhl_apac_form_admin_menu'));
                add_action('admin_post_dhl_malaysia_form_response', array($this, 'dhl_apac_form_response'));
                add_action('redirect_post_location', array($this, 'dhl_apac_redirect_pickup_post_location'));
                add_filter('enter_title_here', array($this, 'dhl_apac_pickup_post_type_changed_add_title_place_holder'), 20, 2);
                add_filter('login_redirect', array($this, 'dhl_apac_him_admin_login_redirect_to_form_connection'), 10, 3);
                add_action('admin_notices', array($this, 'dhl_apac_order_edit_notice'));
            } else {
                // Throw an admin error informing the user this plugin needs WooCommerce to function
                add_action('admin_notices', array($this, 'dhl_apac_notice_wc_required'));
            }

        }

        /**
         * Load Plugin Textdomain
         */
        public function dhl_apac_load_plugin_textdomain()
        {
            load_plugin_textdomain('dhl-ecommerce-apac', false, DHL_APAC_PLUGIN_URL . 'languages');
        }

        /**
         * Admin error notifying user that WC is required
         */
        public function dhl_apac_notice_wc_required()
        {
            ?>
				<div class="error">
					<p><?php esc_html_e('WooCommerce DHL Integration requires WooCommerce to be installed and activated!', 'dhl-ecommerce-apac');?></p>
				</div>
			<?php
        }

        /**
         * Main Hyperlink Infosystem DHL Ecommerce APAC Instance.
         *
         * Ensures only one instance of Hyperlink Infosystem DHL Ecommerce APAC is loaded or can be loaded.
         *
         * @since 1.0
         * @package Hyperlink Infosystem DHL Ecommerce APAC
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Define Hyperlink Infosystem DHL Ecommerce APAC Constants.
         *
         * @since 1.0
         * @package Hyperlink Infosystem DHL Ecommerce APAC
         */
        private function dhl_apac_define_constants()
        {

            // Define plugin version
            $this->define('DHL_APAC_VERSION', $this->version);

            // Define front css url
            $this->define('DHL_APAC_FRONT_CSS_URL', DHL_APAC_PLUGIN_URL . 'assets/css/');

            // Define front js url
            $this->define('DHL_APAC_FRONT_JS_URL', DHL_APAC_PLUGIN_URL . 'assets/js/');

            // Define front images url
            $this->define('DHL_APAC_FRONT_IMAGES_URL', DHL_APAC_PLUGIN_URL . 'assets/images/');

        }

        /**
         * Define constant if not already set.
         *
         * @since 1.0
         * @package Hyperlink Infosystem DHL Ecommerce APAC
         */
        private function define($name, $value)
        {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        /**
         * What type of request is this?
         *
         * @since 1.0
         * @param  string $type admin, ajax, cron or frontend.
         * @return bool
         */
        private function is_request($type)
        {
            switch ($type) {
                case 'admin':
                    return is_admin();
                case 'ajax':
                    return defined('DOING_AJAX');
                case 'cron':
                    return defined('DOING_CRON');
                case 'frontend':
                    return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON') && !defined('REST_REQUEST');
            }
        }

        /**
         * Included files main function
         *
         * @since 1.0
         * @package Hyperlink Infosystem DHL Ecommerce APAC
         */
        public function dhl_apac_includes()
        {

            if ($this->is_request('admin')) {

                $general_included_files = array(
                    'classes/class-dhl-apac-custom-post-types',
                    'classes/class-dhl-apac-custom-post-custom-field',
                    'inc/dhl-apac-extra-function',
                    'shippinglabel/class-dhl-apac-shippinglabel',
                    'shippinglabel/createlable/class-dhl-apac-createlabel',
                    'shippinglabel/createlable/class-dhl-apac-createlog',
                    'multipleShippingLabel/class-dhl-apac-multipleshippinglabel',
                );

                foreach ($general_included_files as $key => $value) {
                    include_once DHL_APAC_PLUGIN_PATH . 'includes/' . $value . '.php';
                }

            }
        }

        /**
         * Included JS & CSS files for frontend main function
         *
         * @since 1.0
         * @package Hyperlink Infosystem DHL Ecommerce APAC
         */
        public function dhl_apac_front_style_js_includes()
        {

            // Register Styles
            wp_register_style(
                'him-dhl-admin-css',
                DHL_APAC_FRONT_CSS_URL . 'dhl-apac-admin-form.css',
                array(),
                $this->version
            );

            // Enqueue Styles
            wp_enqueue_style('him-dhl-admin-css');

            // Register Styles
            wp_register_script(
                'him-dhl-admin-js',
                DHL_APAC_FRONT_JS_URL . 'dhl-apac-him-admin.js',
                array(),
                $this->version
            );

            wp_register_script(
                'him-dhl-jszip-js',
                DHL_APAC_FRONT_JS_URL . 'dhl-apac-him-jszip.js',
                array(),
                $this->version
            );

            // Enqueue Styles
            wp_enqueue_script('him-dhl-admin-js');
            wp_enqueue_script('him-dhl-jszip-js');

            $him_localize['admin_url'] = admin_url('admin-ajax.php');
            wp_localize_script(

                'him-dhl-admin-js',

                'himVars',

                $him_localize

            );
        }

        /**
         * Add Back-end side Menu.
         *
         * @return string
         */
        public function dhl_apac_form_admin_menu()
        {
            add_menu_page(
                __('DHLeCS', 'dhl-ecommerce-apac'),
                'DHLeCS',
                'manage_options',
                'custompage',
                array(&$this, 'dhl_apac_create_custom_form'),
                'dashicons-editor-expand',
                99
            );
        }

        /**
         * DHL Malaysia Form..
         *
         * @return meta value
         */
        public function dhl_apac_create_custom_form()
        {

            if (is_user_logged_in()) {

                $current_user_id                = get_current_user_id();

                // Generate a custom nonce value.
                $dhl_add_user_meta_form_nonce   = wp_create_nonce('dhl_add_user_meta_form_nonce');

                //Form data get using user meta
                $enable_dhl_shipping_lable      = get_user_meta($current_user_id, 'enable_dhl_shipping_lable', true);
                $dhl_country                    = get_user_meta($current_user_id, 'dhl_country', true);
                $dhl_account_type               = get_user_meta($current_user_id, 'dhl_account_type', true);
                $dhl_soldto_account             = get_user_meta($current_user_id, 'dhl_soldto_account', true);
                $dhl_pickup_account             = get_user_meta($current_user_id, 'dhl_pickup_account', true);
                $dhl_product_code               = get_user_meta($current_user_id, 'dhl_product_code', true);
                $dhl_prefix                     = get_user_meta($current_user_id, 'dhl_prefix', true);
                $dhl_client_id                  = get_user_meta($current_user_id, 'dhl_client_id', true);
                $dhl_client_secret_passworrd    = get_user_meta($current_user_id, 'dhl_client_secret_passworrd', true);
                $dhl_lable_template             = get_user_meta($current_user_id, 'dhl_lable_template', true);
                $dhl_lable_format               = get_user_meta($current_user_id, 'dhl_lable_format', true);

                $enable_dhl_shipping_lable      = ($enable_dhl_shipping_lable) ? $enable_dhl_shipping_lable : '';
                $dhl_country                    = ($dhl_country) ? $dhl_country : '';
                $dhl_account_type               = ($dhl_account_type) ? $dhl_account_type : '';
                $dhl_soldto_account             = ($dhl_soldto_account) ? $dhl_soldto_account : '';
                $dhl_product_code               = ($dhl_product_code) ? $dhl_product_code : '';
                $dhl_prefix                     = ($dhl_prefix) ? $dhl_prefix : '';
                $dhl_client_id                  = ($dhl_client_id) ? $dhl_client_id : '';
                $dhl_client_secret_passworrd    = ($dhl_client_secret_passworrd) ? $dhl_client_secret_passworrd : '';
                $dhl_lable_template             = ($dhl_lable_template) ? $dhl_lable_template : '';
                $dhl_lable_format               = ($dhl_lable_format) ? $dhl_lable_format : '';

                ?>
				<div class="h-container-fluid">
					<h2><?php esc_html_e('Configuration Settings', 'dhl-ecommerce-apac');?></h2>
					<div class="dhl_malaysia_add_user_meta_form">
						<form class="h-frm" action="<?php echo esc_url(admin_url('admin-post.php?page=custompage')); ?>" method="post" id="dhl_malaysia_add_user_meta_form" name="dhl_malaysia_add_user_meta_form" >

							<input type="hidden" name="action" value="<?php echo esc_attr('dhl_malaysia_form_response'); ?>">
							<input type="hidden" name="dhl_add_user_meta_nonce" value="<?php echo esc_attr($dhl_add_user_meta_form_nonce); ?>" />

							<!-- Enable Lable field -->

							<div class="h-row">
								<!--Enable DHL Shipping Label-->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="enable_dhl_shipping_lable"><?php esc_html_e('Enable DHL Shipping Label', 'dhl-ecommerce-apac');?></label>
										<select class="form-control" id="enable_dhl_shipping_lable" name="enable_dhl_shipping_lable">
											<option value="<?php echo esc_attr('yes'); ?>" <?php selected($enable_dhl_shipping_lable, 'yes');?>><?php esc_html_e('Yes', 'dhl-ecommerce-apac');?></option>
											<option value="<?php echo esc_attr('no'); ?>" <?php selected($enable_dhl_shipping_lable, 'no');?>><?php esc_html_e('No', 'dhl-ecommerce-apac');?></option>
										</select>
									</div>
								</div>
								<!-- Country field -->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_country"><?php esc_html_e('Country', 'dhl-ecommerce-apac');?></label>
										<select class="form-control" id="dhl_country" name="dhl_country">
											<option value="" <?php selected($dhl_country, '');?>><?php esc_html_e('Please select', 'dhl-ecommerce-apac');?></option>
											<option value="<?php echo esc_attr('malaysia'); ?>" <?php selected($dhl_country, 'malaysia');?>><?php esc_html_e('Malaysia', 'dhl-ecommerce-apac');?></option>
											<option value="<?php echo esc_attr('thailand'); ?>" <?php selected($dhl_country, 'thailand');?>><?php esc_html_e('Thailand', 'dhl-ecommerce-apac');?></option>
										</select>
									</div>
								</div>
								<!-- Account Type field -->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_account_type"><?php esc_html_e('Account Type', 'dhl-ecommerce-apac');?></label>
										<input class="form-control" type="text" id="dhl_account_type" name="dhl_account_type" value="<?php echo esc_attr('DHL eCommerce Asia'); ?>" readonly>
									</div>
								</div>
								<!-- Soldto account field -->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_soldto_account"><?php esc_html_e('Soldto Account', 'dhl-ecommerce-apac');?></label>
										<input class="form-control" type="text" id="dhl_soldto_account" name="dhl_soldto_account" value="<?php echo esc_attr($dhl_soldto_account); ?>">
										<br/><p class="error" style="color: red; display: none"><?php esc_html_e('* Only digits allowed', 'dhl-ecommerce-apac');?></p>
									</div>
								</div>
								<!-- Pickup account field -->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<div class="h-d-flex h-justify-content-between">
											<label for="dhl_pickup_account"><?php esc_html_e('Pickup Account', 'dhl-ecommerce-apac');?></label>
											<a href="<?php echo esc_url(admin_url('/post-new.php?post_type=him_pickup_account')); ?>">
											<?php esc_html_e('Add pickup account', 'dhl-ecommerce-apac');?></a>
										</div>
										<select class="form-control" name="dhl_pickup_account" id="dhl_pickup_account">
											<option value=""><?php esc_html_e('Please select', 'dhl-ecommerce-apac');?></option>
											<?php
                                                global $post;
                                                $defaultaddresssid = array();

                                                //Default pickup addreess checked
                                                $pickup_args = array(
                                                    'numberposts' => -1,
                                                    'post_type' => 'him_pickup_account',
                                                    'order' => 'ASC',
                                                    'meta_query' => array(
                                                        array(
                                                            'key' => '_him_default_address_field',
                                                            'value' => 'on',
                                                            'compare' => '=',
                                                        ),
                                                    ),
                                                );

                                                $pickup_post = get_posts($pickup_args);

                                                if (!empty($pickup_post)) {
                                                    foreach ($pickup_post as $val) {
                                                        setup_postdata($val);
                                                        $defaultaddresssid[] = $val->ID;
                                                    }
                                                }

                                                $args = array(
                                                    'post_type' => 'him_pickup_account',
                                                    'order' => 'ASC',
                                                    'post_status' => 'publish',
                                                    'numberposts' => -1,
                                                );
                                                $posts = get_posts($args);
                                                foreach ($posts as $post):
                                                    setup_postdata($post);
                                                    $him_pickup_account_field   = get_post_meta($post->ID, '_him_pickup_account_field', true);
                                                    $him_pickup_your_name_field = get_post_meta($post->ID, '_him_pickup_your_name_field', true);
                                                    if (!empty($defaultaddresssid) && isset($defaultaddresssid)) {
                                                        $selectedval = ($defaultaddresssid[0] == $post->ID) ? ' selected="selected"' : '';
                                                    } else {
                                                        $selectedval = ($dhl_pickup_account == $post->ID) ? ' selected="selected"' : '';
                                                    }
                                                ?>
                                                    <option value="<?php echo esc_attr($post->ID); ?>" <?php echo esc_attr($selectedval); ?>><?php echo esc_html($him_pickup_your_name_field); ?> - <?php echo esc_html($him_pickup_account_field); ?></option>
                                                <?php endforeach;?>
										</select>
									</div>
								</div>
								<!-- Product Code field -->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_product_code"><?php esc_html_e('Product Code', 'dhl-ecommerce-apac');?></label>
										<select class="form-control" id="dhl_product_code" name="dhl_product_code">
    										<option value="" <?php selected($dhl_product_code, '');?>><?php esc_html_e('Please select', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('PDO'); ?>" <?php selected($dhl_product_code, 'PDO');?>><?php esc_html_e('Parcel Domestic', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('PDR'); ?>" <?php selected($dhl_product_code, 'PDR');?>><?php esc_html_e('DHL Parcel Return', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('PDE'); ?>" <?php selected($dhl_product_code, 'PDE');?>><?php esc_html_e('Parcel Domestic Expedited', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('DDO'); ?>" <?php selected($dhl_product_code, 'DDO');?>><?php esc_html_e('Document Domestic', 'dhl-ecommerce-apac');?></option>
    										<option value="<?php echo esc_attr('SDP'); ?>" <?php selected($dhl_product_code, 'SDP');?>><?php esc_html_e('DHL Parcel Metro', 'dhl-ecommerce-apac');?></option>
										</select>
									</div>
								</div>
								<!-- Prefix field -->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_prefix"><?php esc_html_e('Prefix', 'dhl-ecommerce-apac');?></label>
										<input class="form-control" type="text" id="dhl_prefix" name="dhl_prefix" value="<?php echo esc_attr($dhl_prefix); ?>">
									</div>
								</div>
								<!-- Client id field -->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_client_id"><?php esc_html_e('Client Id', 'dhl-ecommerce-apac');?></label>
										<input class="form-control" type="text" id="dhl_client_id" name="dhl_client_id" value="<?php echo esc_attr($dhl_client_id); ?>">
									</div>
								</div>
								<!-- Client secret/sassworrd field -->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_client_secret_passworrd"><?php esc_html_e('Client Secret/Password', 'dhl-ecommerce-apac');?></label>
										<input class="form-control" type="text" id="dhl_client_secret_passworrd" name="dhl_client_secret_passworrd" value="<?php echo esc_attr($dhl_client_secret_passworrd); ?>">
									</div>
								</div>
								<!-- Test Connection check -->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_client_secret_passworrd"><?php esc_html_e('Test Connection', 'dhl-ecommerce-apac');?></label>
										<div class="test-connection-check">
											<div class="h-d-flex">
												<button class="button testconnectioncheck" type='button'><?php esc_html_e('Test Connection', 'dhl-ecommerce-apac');?></button>
												<div class="loading-icon h-ml-3"><img width="27" height="27" src="<?php echo esc_url(DHL_APAC_FRONT_IMAGES_URL . '/fancybox_loading.svg') ?>" alt="<?php echo esc_attr( 'Loading', 'dhl-ecommerce-apac' ); ?>"></div>
											</div>
											<div class="him-test-connection-message"></div>
										</div>
										<input type="hidden" name="hiddencliendid" value="<?php echo esc_attr($dhl_client_id); ?>">
										<input type="hidden" name="hiddencliendsecretid" value="<?php echo esc_attr($dhl_client_secret_passworrd); ?>">
									</div>
								</div>
								<!-- Auth api -->
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_client_secret_passworrd"><?php esc_html_e('Status', 'dhl-ecommerce-apac');?></label>
										<?php
                                            $user = DHL_APAC_Check_Client_Auth_APi($dhl_client_id, $dhl_client_secret_passworrd);
                                            _e( $user );
                                        ?>
									</div>
								</div>
							</div>

							<div class="h-row">
								<div class="h-col-12">
									<hr>
								</div>
								<div class="h-col-12">
									<h2 class="h-mb-4"><?php esc_html_e('Optional Settings', 'dhl-ecommerce-apac');?></h2>
								</div>
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_lable_template"><?php esc_html_e('Label Template', 'dhl-ecommerce-apac');?></label>
										<select class="form-control" id="dhl_lable_template" name="dhl_lable_template">
											<option value="<?php echo esc_attr('1x1'); ?>" <?php selected($dhl_lable_template, '1x1');?>><?php esc_html_e('1x1', 'dhl-ecommerce-apac');?></option>
											<option value="<?php echo esc_attr('4x1'); ?>" <?php selected($dhl_lable_template, '4x1');?>><?php esc_html_e('4x1', 'dhl-ecommerce-apac');?></option>
										</select>
									</div>
								</div>
								<div class="h-col-4 h-col-sm-4 h-col-md-4 h-col-lg-4 h-col-xl-4">
									<div class="form-group">
										<label for="dhl_lable_format"><?php esc_html_e('Label Format', 'dhl-ecommerce-apac');?></label>
										<select class="form-control" id="dhl_lable_format" name="dhl_lable_format">
											<option value="<?php echo esc_attr('pdf'); ?>" <?php selected($dhl_lable_format, 'pdf');?>><?php esc_html_e('PDF', 'dhl-ecommerce-apac');?></option>
											<option value="<?php echo esc_attr('png'); ?>" <?php selected($dhl_lable_format, 'png');?>><?php esc_html_e('PNG', 'dhl-ecommerce-apac');?></option>
										</select>
									</div>
								</div>
							</div>

							<div class="h-row h-mt-3">
								<div class="h-col-12">
									<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr( 'Save Form', 'dhl-ecommerce-apac' ); ?>">
								</div>
							</div>

						</form>
					</div>
				</div>
				<?php

            }

        }

        /**
         * DHL Malaysia Form..
         *
         * @return Form Response
         */
        public function dhl_apac_form_response()
        {

            $current_user_id = get_current_user_id();

            if (isset($_POST['dhl_add_user_meta_nonce']) && wp_verify_nonce($_POST['dhl_add_user_meta_nonce'], 'dhl_add_user_meta_form_nonce')) {

                $enable_dhl_shipping_lable      = isset($_POST['enable_dhl_shipping_lable']) ? sanitize_text_field($_POST['enable_dhl_shipping_lable']) : '';
                $dhl_country                    = isset($_POST['dhl_country']) ? sanitize_text_field($_POST['dhl_country']) : '';
                $dhl_account_type               = isset($_POST['dhl_account_type']) ? sanitize_text_field($_POST['dhl_account_type']) : '';
                $dhl_soldto_account             = isset($_POST['dhl_soldto_account']) ? sanitize_text_field($_POST['dhl_soldto_account']) : '';
                $dhl_pickup_account             = isset($_POST['dhl_pickup_account']) ? sanitize_text_field($_POST['dhl_pickup_account']) : '';
                $dhl_product_code               = isset($_POST['dhl_product_code']) ? sanitize_text_field($_POST['dhl_product_code']) : '';
                $dhl_prefix                     = isset($_POST['dhl_prefix']) ? sanitize_text_field($_POST['dhl_prefix']) : '';
                $dhl_client_id                  = isset($_POST['dhl_client_id']) ? sanitize_text_field($_POST['dhl_client_id']) : '';
                $dhl_client_secret_passworrd    = isset($_POST['dhl_client_secret_passworrd']) ? sanitize_text_field($_POST['dhl_client_secret_passworrd']) : '';
                $dhl_lable_template             = isset($_POST['dhl_lable_template']) ? sanitize_text_field($_POST['dhl_lable_template']) : '';
                $dhl_lable_format               = isset($_POST['dhl_lable_format']) ? sanitize_text_field($_POST['dhl_lable_format']) : '';

                if (!empty($enable_dhl_shipping_lable)) {

                    update_user_meta($current_user_id, 'enable_dhl_shipping_lable', $enable_dhl_shipping_lable);

                }

                if (!empty($dhl_country)) {

                    update_user_meta($current_user_id, 'dhl_country', $dhl_country);

                }

                if (!empty($dhl_account_type)) {

                    update_user_meta($current_user_id, 'dhl_account_type', $dhl_account_type);

                }

                if (!empty($dhl_soldto_account)) {

                    update_user_meta($current_user_id, 'dhl_soldto_account', $dhl_soldto_account);

                }

                if (!empty($dhl_pickup_account)) {

                    update_user_meta($current_user_id, 'dhl_pickup_account', $dhl_pickup_account);

                }

                if (!empty($dhl_product_code)) {

                    update_user_meta($current_user_id, 'dhl_product_code', $dhl_product_code);

                }

                if (!empty($dhl_prefix)) {

                    update_user_meta($current_user_id, 'dhl_prefix', $dhl_prefix);

                }

                if (!empty($dhl_client_id)) {

                    update_user_meta($current_user_id, 'dhl_client_id', $dhl_client_id);

                }

                if (!empty($dhl_client_secret_passworrd)) {

                    update_user_meta($current_user_id, 'dhl_client_secret_passworrd', $dhl_client_secret_passworrd);

                }

                if (!empty($dhl_lable_template)) {

                    update_user_meta($current_user_id, 'dhl_lable_template', $dhl_lable_template);

                }

                if (!empty($dhl_lable_format)) {

                    update_user_meta($current_user_id, 'dhl_lable_format', $dhl_lable_format);

                }

            }

            $this->redirectToForm();

        }

        /**
         * DHL Malaysia Form..
         *
         * @return Form submit after redirect url
         */
        public function redirectToForm()
        {
            wp_redirect(admin_url('admin.php?page=custompage'));
            exit;
        }

        /**
         * DHL Malaysia Form..
         *
         * @return Pickup post publish redirect location code
         */
        public function dhl_apac_redirect_pickup_post_location($location)
        {

            if ('him_pickup_account' == get_post_type()) {

                if (isset($_POST['save']) || isset($_POST['publish'])) {

                    if (isset($_POST['ID'])) {

                        //sanitize ID field
                        $sanitize_pickup_post_ID                    = isset($_POST['ID']) ? sanitize_text_field($_POST['ID']) : '';
                        $him_dhl_pickup_post_id                     = $sanitize_pickup_post_ID;
                        $dhl_him_posts_id_array_him_pickup_account  = dhl_him_posts_id_array_him_pickup_account();

                        if (!empty($dhl_him_posts_id_array_him_pickup_account)) {

                            foreach ($dhl_him_posts_id_array_him_pickup_account as $dhl_him_posts_id_array_him_pickup_account_value) {

                                if (!empty($him_dhl_pickup_post_id)) {

                                    if ($him_dhl_pickup_post_id == $dhl_him_posts_id_array_him_pickup_account_value) {

                                        $him_default_address_field = get_post_meta($dhl_him_posts_id_array_him_pickup_account_value, '_him_default_address_field', true);

                                        if ($him_default_address_field == 'on') {
                                            update_post_meta($dhl_him_posts_id_array_him_pickup_account_value, '_him_default_address_field', 'on');
                                        }

                                    } else {
                                        update_post_meta($dhl_him_posts_id_array_him_pickup_account_value, '_him_default_address_field', false);
                                    }

                                }

                            }

                        }

                    }

                    return admin_url('admin.php?page=custompage');
                }
            }

            return $location;
        }

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC Post Type Override Placeholder Title.
         *
         * @return Pickup post publish redirect location code
         */
        public function dhl_apac_pickup_post_type_changed_add_title_place_holder($title, $post)
        {

            if ($post->post_type == 'him_pickup_account') {

                $my_title   = esc_html__( 'Company Name', 'dhl-ecommerce-apac' );
                
                return $my_title;
            }

            return $title;

        }

        /**
         * Extra Function File Call.
         *
         * @return Dhl Him Login redirect to form connection
         */
        public function dhl_apac_him_admin_login_redirect_to_form_connection($redirect_to, $request, $user)
        {

            if (isset($user->roles) && is_array($user->roles)) {

                if (in_array('administrator', $user->roles)) {
                    $redirect_to = get_admin_url() . 'admin.php?page=custompage';
                    return $redirect_to;
                } else {
                    return home_url();
                }
            } else {
                return $redirect_to;
            }
        }

        /**
         * Order Edit Page Notice File Call.
         *
         * @return Dhl Him Order Edit Notice
         */
        public function dhl_apac_order_edit_notice()
        {

            $log_path = admin_url('admin.php?page=wc-status&tab=logs');

            if (get_post_type() == 'shop_order') {
                ?>
				<div class="notice is-dismissible notice-warning">
				    <p><?php esc_html_e('If you want to create the label, adding a mandatory shipping address is required!', 'dhl-ecommerce-apac');?></p>
                    <p><?php _e('Please click here to view your DHL label log! <a href="'.$log_path.'" target="_blank">Click Here</>', 'dhl-ecommerce-apac');?></p>
				</div>
				<?php
            }
        }

        /**
         * Get the plugin path.
         *
         * @return string
         */
        public function plugin_path()
        {
            return untrailingslashit(plugin_dir_path(DHL_APAC_PLUGIN_FILE));
        }

        /**
         * Set precision.
         *
         * @return string
         */
        public function plugin_init_set_phpversion()
        {
            if (version_compare(phpversion(), '7.1', '>=')) {
                ini_set('serialize_precision', -1);
            }
        }

    }
}