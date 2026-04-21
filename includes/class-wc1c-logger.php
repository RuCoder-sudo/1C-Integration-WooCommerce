<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WC1C_Logger {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function log( $message, $level = 'info', $context = array() ) {
		if ( get_option( 'wc1c_enable_logging', '1' ) !== '1' ) {
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'wc1c_logs';
		$wpdb->insert(
			$table,
			array(
				'log_level'   => sanitize_text_field( $level ),
				'log_message' => sanitize_text_field( $message ),
				'log_context' => ! empty( $context ) ? wp_json_encode( $context, JSON_UNESCAPED_UNICODE ) : null,
			),
			array( '%s', '%s', '%s' )
		);
	}

	public function info( $message, $context = array() ) {
		$this->log( $message, 'info', $context );
	}

	public function error( $message, $context = array() ) {
		$this->log( $message, 'error', $context );
	}

	public function warning( $message, $context = array() ) {
		$this->log( $message, 'warning', $context );
	}

	public function success( $message, $context = array() ) {
		$this->log( $message, 'success', $context );
	}

	public function get_logs( $per_page = 50, $page = 1, $level = '' ) {
		global $wpdb;
		$table  = $wpdb->prefix . 'wc1c_logs';
		$offset = ( $page - 1 ) * $per_page;
		$where  = '';

		if ( ! empty( $level ) ) {
			$where = $wpdb->prepare( ' WHERE log_level = %s', $level );
		}

		$logs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table}{$where} ORDER BY log_time DESC LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);

		$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$table}{$where}" );

		return array(
			'logs'  => $logs,
			'total' => (int) $total,
		);
	}

	public function clear_logs() {
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wc1c_logs" );
	}
}
