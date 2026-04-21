<?php
/**
 * Uninstall 1C-Integration-WooCommerce
 * Runs when the plugin is deleted (not just deactivated).
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Remove plugin options
$options = array(
	'wc1c_license_key',
	'wc1c_license_status',
	'wc1c_license_attempts',
	'wc1c_enable_exchange',
	'wc1c_exchange_user',
	'wc1c_exchange_pass',
	'wc1c_file_size_limit',
	'wc1c_time_limit',
	'wc1c_use_zip',
	'wc1c_enable_logging',
	'wc1c_export_orders',
	'wc1c_export_order_statuses',
	'wc1c_update_name',
	'wc1c_update_description',
	'wc1c_update_sku',
	'wc1c_update_categories',
	'wc1c_update_attributes',
	'wc1c_update_images',
	'wc1c_update_prices',
	'wc1c_update_stock',
	'wc1c_base_price_type',
	'wc1c_sale_price_type',
	'wc1c_price_types',
	'wc1c_search_by_sku',
	'wc1c_find_category_by_name',
	'wc1c_exclude_warehouses',
);

foreach ( $options as $option ) {
	delete_option( $option );
}

// Drop logs table
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wc1c_logs" );
