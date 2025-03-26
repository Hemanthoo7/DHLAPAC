<?php
/**
 * DHL_APAC_Order_Page_CreateLog setup
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
 * @class DHL_APAC_Order_Page_CreateLog
 */

if (!class_exists('DHL_APAC_Order_Page_CreateLog')) {

    class DHL_APAC_Order_Page_CreateLog {

        private $debug;

        /**
         * WC_DHL_Logger constructor.
         *
         * @param WC_XR_debug $debug
         */
        public function __construct( $debug ) {
            $this->debug = $debug;
        }

        /**
         * Check if logging is enabled
         *
         * @return bool
         */
        public function is_enabled() {

            // Check if debug is on
            if ( 'yes' === $this->debug ) {
                return true;
            }

            return false;
        }

        /**
         * Write the message to log
         *
         * @param String $message
         */
        public function write( $message ) {

            // Check if enabled
            if ( $this->is_enabled() ) {

                // Logger object
                $wc_logger = new WC_Logger();

                // Add to logger
                $wc_logger->add( 'DHL_APAC', $message );
            }

        }

    }
}