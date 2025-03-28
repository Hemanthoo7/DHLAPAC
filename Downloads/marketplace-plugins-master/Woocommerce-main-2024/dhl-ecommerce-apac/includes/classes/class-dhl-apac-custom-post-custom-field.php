<?php
/**
 * Hyperlink Infosystem DHL Ecommerce APAC custom Fiels
 *
 * @package Hyperlink Infosystem DHL Ecommerce APAC
 * @since   1.0
 */

/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {exit;}

/**
 * Main Hyperlink Infosystem DHL Ecommerce APAC custom field.
 *
 * @class DHL_APAC_Pickup_Account_Custom_Field
 */
if (!class_exists('DHL_APAC_Pickup_Account_Custom_Field')) {

    class DHL_APAC_Pickup_Account_Custom_Field
    {

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC custom post types constructor.
         */
        public function __construct()
        {

            add_action('add_meta_boxes', array($this, 'dhl_apac_pickup_account_add_custom_field'));
            add_action('save_post', array($this, 'dhl_apac_pickup_account_post_type_save_custom_field'));
        }

        /**
         * Hyperlink Infosystem DHL Ecommerce APAC Post custom field.
         */
        public function dhl_apac_pickup_account_add_custom_field($post_type)
        {

            /**
             * Hyperlink Limit meta box to certain post types.
             */
            $post_types = array('him_pickup_account');

            if (in_array($post_type, $post_types)) {
                add_meta_box(
                    'him_jobs_box',
                    __('Pickup Account Information', 'dhl-ecommerce-apac'),
                    [$this, 'dhl_apac_pickup_account_post_render_meta_box_content'],
                    $post_type,
                    'advanced',
                    'high'
                );
            }

        }

        /**
         * Hyperlink pickup account save the meta when the post is saved.
         */
        public function dhl_apac_pickup_account_post_type_save_custom_field($post_id)
        {

            /**
             * We need to verify this came from the our screen and with proper authorization,
             * because save_post can be triggered at other times.
             */

            // Check if our nonce is set.

            $him_pickup_account_post_inner_custom_box_nonce = sanitize_text_field( $_POST['him_pickup_account_post_inner_custom_box_nonce'] );
            $post_type 										= sanitize_text_field( $_POST['post_type'] );

            // Verify that the nonce is valid.
            if (!wp_verify_nonce($him_pickup_account_post_inner_custom_box_nonce, 'him_pickup_account_post_inner_custom_box')) {
                return $post_id;
            }

            /*
             * If this is an autosave, our form has not been submitted,
             * so we don't want to do anything.
             */
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }

            // Check the user's permissions.
            if ('him_pickup_account' == $post_type) {
                if (!current_user_can('edit_page', $post_id)) {
                    return $post_id;
                }
            } else {
                if (!current_user_can('edit_post', $post_id)) {
                    return $post_id;
                }
            }

            /* OK, it's safe for us to save the data now. */

            // Sanitize the Job field.
            $him_pickup_account_field 		= sanitize_text_field($_POST['him_pickup_account_field']);
            $him_pickup_your_name_field 	= sanitize_text_field($_POST['him_pickup_your_name_field']);
            $him_address_one_field 			= sanitize_text_field($_POST['him_address_one_field']);
            $him_address_two_field 			= sanitize_text_field($_POST['him_address_two_field']);
            $him_address_three_field 		= sanitize_text_field($_POST['him_address_three_field']);
            $him_city_field 				= sanitize_text_field($_POST['him_city_field']);
            $him_state_field 				= sanitize_text_field($_POST['him_state_field']);
            $him_district_field 			= sanitize_text_field($_POST['him_district_field']);
            $him_phone_field 				= sanitize_text_field($_POST['him_phone_field']);
            $him_postcode_field 			= sanitize_text_field($_POST['him_postcode_field']);
            $him_email_field 				= sanitize_email($_POST['him_email_field']);
            $him_default_address_field 		= sanitize_text_field($_POST['him_default_address_field']);

            // Update the Job meta field.
            update_post_meta($post_id, '_him_pickup_account_field', $him_pickup_account_field);
            update_post_meta($post_id, '_him_pickup_your_name_field', $him_pickup_your_name_field);
            update_post_meta($post_id, '_him_address_one_field', $him_address_one_field);
            update_post_meta($post_id, '_him_address_two_field', $him_address_two_field);
            update_post_meta($post_id, '_him_address_three_field', $him_address_three_field);
            update_post_meta($post_id, '_him_city_field', $him_city_field);
            update_post_meta($post_id, '_him_state_field', $him_state_field);
            update_post_meta($post_id, '_him_district_field', $him_district_field);
            update_post_meta($post_id, '_him_postcode_field', $him_postcode_field);
            update_post_meta($post_id, '_him_phone_field', $him_phone_field);
            update_post_meta($post_id, '_him_email_field', $him_email_field);
            update_post_meta($post_id, '_him_default_address_field', $him_default_address_field);

        }

        /**
         * Jobs render meta box content.
         */
        public function dhl_apac_pickup_account_post_render_meta_box_content($post)
        {

            // Add an nonce field so we can check for it later.
            wp_nonce_field('him_pickup_account_post_inner_custom_box', 'him_pickup_account_post_inner_custom_box_nonce');

            // Use get_post_meta to retrieve an existing value from the database.
            $him_pickup_account_field 		= get_post_meta($post->ID, '_him_pickup_account_field', true);
            $him_pickup_your_name_field 	= get_post_meta($post->ID, '_him_pickup_your_name_field', true);
            $him_address_one_field 			= get_post_meta($post->ID, '_him_address_one_field', true);
            $him_address_two_field 			= get_post_meta($post->ID, '_him_address_two_field', true);
            $him_address_three_field 		= get_post_meta($post->ID, '_him_address_three_field', true);
            $him_city_field 				= get_post_meta($post->ID, '_him_city_field', true);
            $him_state_field 				= get_post_meta($post->ID, '_him_state_field', true);
            $him_district_field 			= get_post_meta($post->ID, '_him_district_field', true);
            $him_postcode_field 			= get_post_meta($post->ID, '_him_postcode_field', true);
            $him_phone_field 				= get_post_meta($post->ID, '_him_phone_field', true);
            $him_email_field 				= get_post_meta($post->ID, '_him_email_field', true);
            $him_default_address_field 		= get_post_meta($post->ID, '_him_default_address_field', true);

            // Display the form, using the current value.
            ?>
			 <div class="h-container-fluid h-frm">
				<div class="h-row">
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_pickup_account_field">
								<?php esc_html_e('Pickup Account', 'dhl-ecommerce-apac');?>
								<span class="required-field">*</span>
							</label>
							<input type="text" class="form-control" id="him_pickup_account_field" name="him_pickup_account_field" value="<?php echo esc_attr($him_pickup_account_field); ?>" required/>
						</div>
					</div>
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_pickup_your_name_field">
								<?php esc_html_e('Pickup Account Name', 'dhl-ecommerce-apac');?>
								<span class="required-field">*</span>
							</label>
							<input type="text" class="form-control" id="him_pickup_your_name_field" name="him_pickup_your_name_field" value="<?php echo esc_attr($him_pickup_your_name_field); ?>" required/>
						</div>
					</div>
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_address_one_field">
								<?php esc_html_e('Address Line 1', 'dhl-ecommerce-apac');?>
								<span class="required-field">*</span>
							</label>
							<input type="text" class="form-control" id="him_address_one_field" name="him_address_one_field" value="<?php echo esc_attr($him_address_one_field); ?>" required/>
						</div>
					</div>
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_address_two_field">
								<?php esc_html_e('Address Line 2', 'dhl-ecommerce-apac');?>
							</label>
							<input type="text" class="form-control" id="him_address_two_field" name="him_address_two_field" value="<?php echo esc_attr($him_address_two_field); ?>"/>
						</div>
					</div>
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_address_three_field">
								<?php esc_html_e('Address Line 3', 'dhl-ecommerce-apac');?>
							</label>
							<input type="text" class="form-control" id="him_address_three_field" name="him_address_three_field" value="<?php echo esc_attr($him_address_three_field); ?>" />
						</div>
					</div>
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_city_field">
							<?php esc_html_e('City', 'dhl-ecommerce-apac');?>
								<span class="required-field">*</span>
							</label>
							<input type="text" id="him_city_field" class="form-control" name="him_city_field" value="<?php echo esc_attr($him_city_field); ?>" required/>
						</div>
					</div>
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_state_field">
							<?php esc_html_e('State', 'dhl-ecommerce-apac');?>
							</label>
							<input type="text" class="form-control" id="him_state_field" name="him_state_field" value="<?php echo esc_attr($him_state_field); ?>" />
						</div>
					</div>
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_district_field">
							<?php esc_html_e('District', 'dhl-ecommerce-apac');?>
							</label>
							<input type="text" class="form-control" id="him_district_field" name="him_district_field" value="<?php echo esc_attr($him_district_field); ?>"/>
						</div>
					</div>
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_postcode_field">
							<?php esc_html_e('Postcode', 'dhl-ecommerce-apac');?>
								<span class="required-field">*</span>
							</label>
							<input type="text" class="form-control" id="him_postcode_field" name="him_postcode_field" value="<?php echo esc_attr($him_postcode_field); ?>" required/>
						</div>
					</div>
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_phone_field">
							<?php esc_html_e('Phone', 'dhl-ecommerce-apac');?>
								<span class="required-field">*</span>
							</label>
							<input type="text" class="form-control" id="him_phone_field" name="him_phone_field" value="<?php echo esc_attr($him_phone_field); ?>" required/>
						</div>
					</div>
					<div class="h-col-md-6">
						<div class="form-group">
							<label for="him_email_field">
							<?php esc_html_e('Email', 'dhl-ecommerce-apac');?>
								<span class="required-field">*</span>
							</label>
							<input type="text" class="form-control" id="him_email_field" name="him_email_field" value="<?php echo esc_attr($him_email_field); ?>" required/>
						</div>
					</div>
					<div class="h-col-md-6 h-align-self-center">
						<div class="form-group h-mb-0">
							<label for="him_default_address_field" class="h-mb-0">
							<?php esc_html_e('Set as Default', 'dhl-ecommerce-apac');?>
							</label>
								<?php
									$checkedcheck = '';
						            if ($him_default_address_field == 'on') {
						                $checkedcheck = ' checked';
						            } else {
						                $checkedcheck = '';
						            }
					            ?>
							<input class="h-mt-0 h-ml-1" type="checkbox" id="him_default_address_field" name="him_default_address_field"<?php echo esc_attr($checkedcheck); ?>/>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

    }
    new DHL_APAC_Pickup_Account_Custom_Field();
}