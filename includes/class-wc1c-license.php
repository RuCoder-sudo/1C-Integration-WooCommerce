<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WC1C_License {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function is_active() {
		return get_option( WC1C_LICENSE_STATUS_OPTION ) === 'active';
	}

	public function get_key() {
		return get_option( WC1C_LICENSE_KEY_OPTION, '' );
	}

	public function get_attempts_left() {
		$attempts = get_option( WC1C_LICENSE_ATTEMPTS_OPTION, WC1C_MAX_ATTEMPTS );
		return max( 0, (int) $attempts );
	}

	public function activate_license( $license_key ) {
		$attempts = $this->get_attempts_left();

		if ( $attempts <= 0 ) {
			return array(
				'success' => false,
				'message' => __( 'Превышено максимальное количество попыток активации. Обратитесь в поддержку: support@рукодер.рф', WC1C_TEXT_DOMAIN ),
			);
		}

		$license_key = sanitize_text_field( $license_key );

		if ( empty( $license_key ) ) {
			return array(
				'success' => false,
				'message' => __( 'Введите лицензионный ключ.', WC1C_TEXT_DOMAIN ),
			);
		}

		if ( ! preg_match( '/^WC1C-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $license_key ) ) {
			update_option( WC1C_LICENSE_ATTEMPTS_OPTION, $attempts - 1 );
			return array(
				'success' => false,
				'message' => sprintf(
					__( 'Неверный формат ключа. Формат: WC1C-XXXX-XXXX-XXXX-XXXX. Осталось попыток: %d из %d.', WC1C_TEXT_DOMAIN ),
					$attempts - 1,
					WC1C_MAX_ATTEMPTS
				),
			);
		}

		$response = wp_remote_post(
			WC1C_LICENSE_SERVER . '/activate',
			array(
				'timeout' => 15,
				'body'    => array(
					'license_key' => $license_key,
					'site_url'    => home_url(),
					'plugin'      => '1c-integration-woocommerce',
					'version'     => WC1C_VERSION,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			update_option( WC1C_LICENSE_ATTEMPTS_OPTION, $attempts - 1 );
			return array(
				'success' => false,
				'message' => __( 'Ошибка соединения с сервером лицензий. Проверьте подключение к интернету.', WC1C_TEXT_DOMAIN ),
			);
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$code = wp_remote_retrieve_response_code( $response );

		if ( $code === 200 && ! empty( $body['success'] ) ) {
			update_option( WC1C_LICENSE_KEY_OPTION, $license_key );
			update_option( WC1C_LICENSE_STATUS_OPTION, 'active' );
			update_option( WC1C_LICENSE_ATTEMPTS_OPTION, WC1C_MAX_ATTEMPTS );
			return array(
				'success' => true,
				'message' => __( 'Лицензия успешно активирована! Все функции плагина доступны.', WC1C_TEXT_DOMAIN ),
			);
		}

		update_option( WC1C_LICENSE_ATTEMPTS_OPTION, $attempts - 1 );
		$err_msg = ! empty( $body['message'] ) ? $body['message'] : __( 'Ключ не найден или уже используется на другом сайте.', WC1C_TEXT_DOMAIN );
		return array(
			'success' => false,
			'message' => $err_msg . ' ' . sprintf( __( 'Осталось попыток: %d из %d.', WC1C_TEXT_DOMAIN ), $attempts - 1, WC1C_MAX_ATTEMPTS ),
		);
	}

	public function deactivate_license() {
		$license_key = $this->get_key();
		if ( $license_key ) {
			wp_remote_post(
				WC1C_LICENSE_SERVER . '/deactivate',
				array(
					'timeout' => 10,
					'body'    => array(
						'license_key' => $license_key,
						'site_url'    => home_url(),
						'plugin'      => '1c-integration-woocommerce',
					),
				)
			);
		}
		delete_option( WC1C_LICENSE_KEY_OPTION );
		update_option( WC1C_LICENSE_STATUS_OPTION, 'inactive' );
		update_option( WC1C_LICENSE_ATTEMPTS_OPTION, WC1C_MAX_ATTEMPTS );
	}

	public function send_license_request( $name, $email, $consent_personal, $consent_subscribe, $site_url = '', $social_url = '' ) {
		if ( empty( $name ) || empty( $email ) ) {
			return array(
				'success' => false,
				'message' => __( 'Укажите имя и email.', WC1C_TEXT_DOMAIN ),
			);
		}
		if ( ! is_email( $email ) ) {
			return array(
				'success' => false,
				'message' => __( 'Неверный формат email.', WC1C_TEXT_DOMAIN ),
			);
		}
		if ( empty( $consent_personal ) ) {
			return array(
				'success' => false,
				'message' => __( 'Необходимо дать согласие на обработку персональных данных.', WC1C_TEXT_DOMAIN ),
			);
		}

		$site_url   = $site_url ?: home_url();
		$social_url = $social_url ?: '';

		$to      = 'rucoder.rf@yandex.ru';
		$subject = 'Запрос лицензии: 1C-Integration-WooCommerce';
		$body    = "Новый запрос лицензионного ключа для плагина 1C-Integration-WooCommerce.\n\n";
		$body   .= "Имя:             " . sanitize_text_field( $name ) . "\n";
		$body   .= "Email:           " . sanitize_email( $email ) . "\n";
		$body   .= "Сайт:            " . esc_url_raw( $site_url ) . "\n";
		$body   .= "Соц. сети:       " . ( $social_url ? sanitize_text_field( $social_url ) : '—' ) . "\n";
		$body   .= "Версия плагина:  " . WC1C_VERSION . "\n";
		$body   .= "Согласие ПД:     " . ( $consent_personal ? 'Да' : 'Нет' ) . "\n";
		$body   .= "Согласие рассылки: " . ( $consent_subscribe ? 'Да' : 'Нет' ) . "\n";

		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'Reply-To: ' . sanitize_email( $email ),
		);

		$sent = wp_mail( $to, $subject, $body, $headers );

		if ( $sent ) {
			return array(
				'success' => true,
				'message' => __( 'Запрос отправлен! Владелец плагина рассмотрит его и пришлёт ключ на ваш email.', WC1C_TEXT_DOMAIN ),
			);
		}

		return array(
			'success' => false,
			'message' => __( 'Ошибка отправки запроса. Попробуйте написать напрямую: support@рукодер.рф', WC1C_TEXT_DOMAIN ),
		);
	}
}
