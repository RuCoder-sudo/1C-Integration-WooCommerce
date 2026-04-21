<?php
/**
 * Plugin Name: 1C-Integration-WooCommerce
 * Plugin URI: https://рукодер.рф/
 * Description: 1С-Обмен для WooCommerce: интеграция с 1С, СБИС, МойСклад.
 * Version: 1.1.0
 * Author: Сергей Солошенко (РуКодер)
 * Author URI: https://рукодер.рф/
 * License: Proprietary
 * License URI: https://рукодер.рф/
 * Text Domain: 1c-integration-woocommerce
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.6
 * Requires PHP: 7.4
 * Stable tag: 1.0.0
 * Tags: 1c-integration, woocommerce, 1с, мойсклад, сбис
 *
 * Compatible with: WordPress, WooCommerce, SBIS, MoySkla
 * Translation Ready: Yes
 * Locale: ru_RU
 *
 * Features:
 * - Синхронизация товаров, категорий, атрибутов, цен и остатков из 1С
 * - Поддержка СБИС и МойСклад как источника данных
 * - Выгрузка заказов в 1С в режиме реального времени
 * - Гибкие настройки полей синхронизации
 * - Подробные логи обмена данными
 * - Тест подключения к серверу обмена
 *
 * Installation:
 * 1. Загрузите папку `1c-integration-woocommerce` в директорию `/wp-content/plugins/`
 * 2. Активируйте плагин через меню «Плагины» в WordPress
 * 3. Перейдите в «1C Integration → Активация»
 * 4. Введите лицензионный ключ или запросите его
 * 5. После активации настройте параметры обмена
 *
 * FAQ:
 * Q: С какими версиями 1С работает плагин?
 * A: С любой конфигурацией 1С:Предприятие 8, поддерживающей обмен по протоколу CML2/CML3.
 *
 * Q: Работает ли со СБИС и МойСклад?
 * A: Да, оба продукта поддерживаются как источник обмена данными.
 *
 * Support URI: https://рукодер.рф/
 * Разработчик: Сергей Солошенко | РуКодер
 * Специализация: Веб-разработка с 2018 года | WordPress / Full Stack
 * Принцип работы: "Сайт как для себя"
 * Контакты:
 * - Телефон/WhatsApp: +7 (985) 985-53-97
 * - Email: support@рукодер.рф
 * - Telegram: @RussCoder
 * - Портфолио: https://рукодер.рф
 * - GitHub: https://github.com/RuCoder-sudo
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

define( 'WC1C_VERSION', '1.1.0' );
define( 'WC1C_PLUGIN_FILE', __FILE__ );
define( 'WC1C_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WC1C_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WC1C_TEXT_DOMAIN', '1c-integration-woocommerce' );
define( 'WC1C_LICENSE_SERVER', 'https://рукодер.рф/wp-json/rucoder-lm/v1' );
define( 'WC1C_LICENSE_KEY_OPTION', 'wc1c_license_key' );
define( 'WC1C_LICENSE_STATUS_OPTION', 'wc1c_license_status' );
define( 'WC1C_LICENSE_ATTEMPTS_OPTION', 'wc1c_license_attempts' );
define( 'WC1C_MAX_ATTEMPTS', 5 );

require_once WC1C_PLUGIN_DIR . 'includes/class-wc1c-license.php';
require_once WC1C_PLUGIN_DIR . 'includes/class-wc1c-exchange.php';
require_once WC1C_PLUGIN_DIR . 'includes/class-wc1c-importer.php';
require_once WC1C_PLUGIN_DIR . 'includes/class-wc1c-logger.php';
require_once WC1C_PLUGIN_DIR . 'admin/class-wc1c-admin.php';

class WC1C_Plugin {

        private static $instance = null;

        public static function get_instance() {
                if ( null === self::$instance ) {
                        self::$instance = new self();
                }
                return self::$instance;
        }

        private function __construct() {
                add_action( 'init', array( $this, 'load_textdomain' ) );
                add_action( 'plugins_loaded', array( $this, 'init' ) );
                register_activation_hook( WC1C_PLUGIN_FILE, array( $this, 'activate' ) );
                register_deactivation_hook( WC1C_PLUGIN_FILE, array( $this, 'deactivate' ) );
        }

        public function load_textdomain() {
                load_plugin_textdomain( WC1C_TEXT_DOMAIN, false, dirname( plugin_basename( WC1C_PLUGIN_FILE ) ) . '/languages' );
        }

        public function init() {
                if ( ! class_exists( 'WooCommerce' ) ) {
                        add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
                        return;
                }

                $license = WC1C_License::get_instance();

                if ( is_admin() ) {
                        WC1C_Admin::get_instance();
                }

                if ( $license->is_active() ) {
                        WC1C_Exchange::get_instance();
                }
        }

        public function activate() {
                global $wpdb;
                $charset_collate = $wpdb->get_charset_collate();

                $table_logs = $wpdb->prefix . 'wc1c_logs';
                $sql = "CREATE TABLE IF NOT EXISTS $table_logs (
                        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        log_time DATETIME DEFAULT CURRENT_TIMESTAMP,
                        log_level VARCHAR(20) NOT NULL DEFAULT 'info',
                        log_message TEXT NOT NULL,
                        log_context LONGTEXT,
                        PRIMARY KEY (id),
                        KEY log_level (log_level),
                        KEY log_time (log_time)
                ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );

                if ( false === get_option( WC1C_LICENSE_ATTEMPTS_OPTION ) ) {
                        add_option( WC1C_LICENSE_ATTEMPTS_OPTION, WC1C_MAX_ATTEMPTS );
                }
        }

        public function deactivate() {
        }

        public function woocommerce_missing_notice() {
                ?>
                <div class="notice notice-error">
                        <p><?php esc_html_e( 'Плагин 1C-Integration-WooCommerce требует установленного и активного плагина WooCommerce.', WC1C_TEXT_DOMAIN ); ?></p>
                </div>
                <?php
        }
}

WC1C_Plugin::get_instance();
