<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WC1C_Admin {

        private static $instance = null;
        private $license;

        public static function get_instance() {
                if ( null === self::$instance ) {
                        self::$instance = new self();
                }
                return self::$instance;
        }

        private function __construct() {
                $this->license = WC1C_License::get_instance();

                add_action( 'admin_menu', array( $this, 'register_menu' ) );
                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
                add_action( 'admin_post_wc1c_save_settings', array( $this, 'handle_save_settings' ) );
                add_action( 'admin_post_wc1c_activate_license', array( $this, 'handle_activate_license' ) );
                add_action( 'admin_post_wc1c_deactivate_license', array( $this, 'handle_deactivate_license' ) );
                add_action( 'admin_post_wc1c_request_license', array( $this, 'handle_request_license' ) );
                add_action( 'admin_post_wc1c_test_connection', array( $this, 'handle_test_connection' ) );
                add_action( 'admin_post_wc1c_clear_logs', array( $this, 'handle_clear_logs' ) );
        }

        public function register_menu() {
                add_menu_page(
                        __( 'Интеграция 1C', WC1C_TEXT_DOMAIN ),
                        __( '1C Integration', WC1C_TEXT_DOMAIN ),
                        'manage_woocommerce',
                        'wc1c',
                        array( $this, 'render_activation_page' ),
                        'dashicons-networking',
                        56
                );

                add_submenu_page(
                        'wc1c',
                        __( 'Активация', WC1C_TEXT_DOMAIN ),
                        __( 'Активация', WC1C_TEXT_DOMAIN ),
                        'manage_woocommerce',
                        'wc1c',
                        array( $this, 'render_activation_page' )
                );

                if ( $this->license->is_active() ) {
                        add_submenu_page(
                                'wc1c',
                                __( 'Настройки', WC1C_TEXT_DOMAIN ),
                                __( 'Настройки', WC1C_TEXT_DOMAIN ),
                                'manage_woocommerce',
                                'wc1c-settings',
                                array( $this, 'render_settings_page' )
                        );
                        add_submenu_page(
                                'wc1c',
                                __( 'Каталог товаров', WC1C_TEXT_DOMAIN ),
                                __( 'Каталог товаров', WC1C_TEXT_DOMAIN ),
                                'manage_woocommerce',
                                'wc1c-catalog',
                                array( $this, 'render_catalog_page' )
                        );
                        add_submenu_page(
                                'wc1c',
                                __( 'Тест подключения', WC1C_TEXT_DOMAIN ),
                                __( 'Тест подключения', WC1C_TEXT_DOMAIN ),
                                'manage_woocommerce',
                                'wc1c-test',
                                array( $this, 'render_test_page' )
                        );
                        add_submenu_page(
                                'wc1c',
                                __( 'Логи', WC1C_TEXT_DOMAIN ),
                                __( 'Логи', WC1C_TEXT_DOMAIN ),
                                'manage_woocommerce',
                                'wc1c-logs',
                                array( $this, 'render_logs_page' )
                        );
                        add_submenu_page(
                                'wc1c',
                                __( 'Инструкции', WC1C_TEXT_DOMAIN ),
                                __( 'Инструкции', WC1C_TEXT_DOMAIN ),
                                'manage_woocommerce',
                                'wc1c-help',
                                array( $this, 'render_help_page' )
                        );
                }
        }

        public function enqueue_assets( $hook ) {
                if ( strpos( $hook, 'wc1c' ) === false ) {
                        return;
                }
                wp_enqueue_style( 'wc1c-admin', WC1C_PLUGIN_URL . 'admin/admin.css', array(), WC1C_VERSION );
                wp_enqueue_script( 'wc1c-admin', WC1C_PLUGIN_URL . 'admin/admin.js', array( 'jquery' ), WC1C_VERSION, true );
        }

        public function render_activation_page() {
                $notice = $this->get_flash_notice();
                include WC1C_PLUGIN_DIR . 'admin/views/activation.php';
        }

        public function render_settings_page() {
                $notice = $this->get_flash_notice();
                include WC1C_PLUGIN_DIR . 'admin/views/settings.php';
        }

        public function render_catalog_page() {
                include WC1C_PLUGIN_DIR . 'admin/views/catalog.php';
        }

        public function render_test_page() {
                $notice = $this->get_flash_notice();
                include WC1C_PLUGIN_DIR . 'admin/views/test.php';
        }

        public function render_logs_page() {
                $logger = WC1C_Logger::get_instance();
                $page   = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
                $level  = isset( $_GET['log_level'] ) ? sanitize_text_field( $_GET['log_level'] ) : '';
                $data   = $logger->get_logs( 50, $page, $level );
                $notice = $this->get_flash_notice();
                include WC1C_PLUGIN_DIR . 'admin/views/logs.php';
        }

        public function render_help_page() {
                include WC1C_PLUGIN_DIR . 'admin/views/help.php';
        }

        public function handle_save_settings() {
                if ( ! current_user_can( 'manage_woocommerce' ) ) {
                        wp_die( esc_html__( 'You do not have sufficient permissions.', 'default' ) );
                }
                check_admin_referer( 'wc1c_save_settings' );

                $fields = array(
                        'wc1c_enable_exchange'        => 'checkbox',
                        'wc1c_exchange_user'          => 'text',
                        'wc1c_exchange_pass'          => 'text',
                        'wc1c_file_size_limit'        => 'int',
                        'wc1c_time_limit'             => 'int',
                        'wc1c_use_zip'                => 'checkbox',
                        'wc1c_enable_logging'         => 'checkbox',
                        'wc1c_export_orders'          => 'checkbox',
                        'wc1c_update_name'            => 'checkbox',
                        'wc1c_update_description'     => 'checkbox',
                        'wc1c_update_sku'             => 'checkbox',
                        'wc1c_update_categories'      => 'checkbox',
                        'wc1c_update_attributes'      => 'checkbox',
                        'wc1c_update_images'          => 'checkbox',
                        'wc1c_update_prices'          => 'checkbox',
                        'wc1c_update_stock'           => 'checkbox',
                        'wc1c_base_price_type'        => 'text',
                        'wc1c_sale_price_type'        => 'text',
                        'wc1c_search_by_sku'          => 'checkbox',
                        'wc1c_find_category_by_name'  => 'checkbox',
                );

                foreach ( $fields as $key => $type ) {
                        if ( $type === 'checkbox' ) {
                                update_option( $key, isset( $_POST[ $key ] ) ? '1' : '0' );
                        } elseif ( $type === 'int' ) {
                                update_option( $key, absint( $_POST[ $key ] ?? 0 ) );
                        } else {
                                update_option( $key, sanitize_text_field( $_POST[ $key ] ?? '' ) );
                        }
                }

                $this->set_flash_notice( 'success', '✅ Настройки сохранены.' );
                wp_safe_redirect( admin_url( 'admin.php?page=wc1c-settings' ) );
                exit;
        }

        public function handle_activate_license() {
                if ( ! current_user_can( 'manage_woocommerce' ) ) {
                        wp_die( esc_html__( 'You do not have sufficient permissions.', 'default' ) );
                }
                check_admin_referer( 'wc1c_activate_license' );

                $key    = isset( $_POST['license_key'] ) ? strtoupper( sanitize_text_field( $_POST['license_key'] ) ) : '';
                $result = $this->license->activate_license( $key );

                $type = $result['success'] ? 'success' : 'error';
                $this->set_flash_notice( $type, $result['message'] );

                wp_safe_redirect( admin_url( 'admin.php?page=wc1c' ) );
                exit;
        }

        public function handle_deactivate_license() {
                if ( ! current_user_can( 'manage_woocommerce' ) ) {
                        wp_die( esc_html__( 'You do not have sufficient permissions.', 'default' ) );
                }
                check_admin_referer( 'wc1c_deactivate_license' );

                $this->license->deactivate_license();
                $this->set_flash_notice( 'info', 'Лицензия деактивирована.' );

                wp_safe_redirect( admin_url( 'admin.php?page=wc1c' ) );
                exit;
        }

        public function handle_request_license() {
                if ( ! current_user_can( 'manage_woocommerce' ) ) {
                        wp_die( esc_html__( 'You do not have sufficient permissions.', 'default' ) );
                }
                check_admin_referer( 'wc1c_request_license' );

                $name              = sanitize_text_field( $_POST['requester_name'] ?? '' );
                $email             = sanitize_email( $_POST['requester_email'] ?? '' );
                $site_url          = esc_url_raw( $_POST['requester_site'] ?? home_url() );
                $social_url        = sanitize_text_field( $_POST['requester_social'] ?? '' );
                $consent_personal  = ! empty( $_POST['consent_personal'] );
                $consent_subscribe = ! empty( $_POST['consent_subscribe'] );

                $result = $this->license->send_license_request( $name, $email, $consent_personal, $consent_subscribe, $site_url, $social_url );
                $type   = $result['success'] ? 'success' : 'error';
                $this->set_flash_notice( $type, $result['message'] );

                wp_safe_redirect( admin_url( 'admin.php?page=wc1c' ) );
                exit;
        }

        public function handle_test_connection() {
                if ( ! current_user_can( 'manage_woocommerce' ) ) {
                        wp_die( esc_html__( 'You do not have sufficient permissions.', 'default' ) );
                }
                check_admin_referer( 'wc1c_test_connection' );

                $exchange = WC1C_Exchange::get_instance();
                $result   = $exchange->test_connection();
                $type     = $result['success'] ? 'success' : 'error';
                $message  = $result['message'];
                if ( $result['success'] && ! empty( $result['url'] ) ) {
                        $message .= '<br><strong>URL обмена:</strong> <code>' . esc_html( $result['url'] ) . '</code>';
                }
                $this->set_flash_notice( $type, $message );

                wp_safe_redirect( admin_url( 'admin.php?page=wc1c-test' ) );
                exit;
        }

        public function handle_clear_logs() {
                if ( ! current_user_can( 'manage_woocommerce' ) ) {
                        wp_die( esc_html__( 'You do not have sufficient permissions.', 'default' ) );
                }
                check_admin_referer( 'wc1c_clear_logs' );

                WC1C_Logger::get_instance()->clear_logs();
                $this->set_flash_notice( 'success', 'Логи очищены.' );

                wp_safe_redirect( admin_url( 'admin.php?page=wc1c-logs' ) );
                exit;
        }

        private function set_flash_notice( $type, $message ) {
                set_transient( 'wc1c_flash_notice_' . get_current_user_id(), array( 'type' => $type, 'message' => $message ), 60 );
        }

        private function get_flash_notice() {
                $key    = 'wc1c_flash_notice_' . get_current_user_id();
                $notice = get_transient( $key );
                delete_transient( $key );
                return $notice;
        }
}
