<?php
/**
 * Plugin Name: DHL Ecommerce APAC
 * Plugin URI: #
 * Description: DHL Ecommerce APAC integration for DHL eCommerce Malaysia
 * Author: DHL APAC
 * Author URI: #
 * Version: 1.1.1
 * Text Domain: dhl-ecommerce-apac
 * Domain Path: /languages
 * WC requires at least: 3.0
 * WC tested up to: 7.4.1
 * Requires at least: 5.7
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package dhl-ecommerce-apac
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin file
if (!defined('DHL_APAC_PLUGIN_FILE')) {
    define('DHL_APAC_PLUGIN_FILE', __FILE__);
}

// Define plugin path
if (!defined('DHL_APAC_PLUGIN_PATH')) {
    define('DHL_APAC_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

// Define plugin url
if (!defined('DHL_APAC_PLUGIN_URL')) {
    define('DHL_APAC_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Include the main Hyperlink Infosystem DHL Ecommerce APAC class.
if (!class_exists('DHL_APAC')) {
    include_once dirname(__FILE__) . '/includes/class-dhl-apac.php';
}

/**
 * Main instance of DHL_APAC.
 *
 * Returns the main instance of Hyperlink Infosystem DHL Ecommerce APAC to prevent the need to use globals.
 *
 * @since  1.0
 * @return DHL_APAC
 */
function DHL_APAC()
{
    return DHL_APAC::instance();
}

// Global for backwards compatibility.
$GLOBALS['DHL_APAC'] = DHL_APAC();