<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WC1C_Exchange {

        private static $instance = null;
        private $logger;

        public static function get_instance() {
                if ( null === self::$instance ) {
                        self::$instance = new self();
                }
                return self::$instance;
        }

        private function __construct() {
                $this->logger = WC1C_Logger::get_instance();

                if ( get_option( 'wc1c_enable_exchange', '0' ) === '1' ) {
                        add_action( 'init', array( $this, 'handle_exchange_request' ) );
                }
        }

        public function handle_exchange_request() {
                if ( ! isset( $_GET['wc1c_exchange'] ) ) {
                        return;
                }

                $user    = get_option( 'wc1c_exchange_user', '' );
                $pass    = get_option( 'wc1c_exchange_pass', '' );

                if ( empty( $user ) || empty( $pass ) ) {
                        header( 'HTTP/1.1 403 Forbidden' );
                        echo "failure\nНастройте пользователя и пароль обмена в настройках плагина.";
                        exit;
                }

                if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) || ! isset( $_SERVER['PHP_AUTH_PW'] ) ) {
                        header( 'WWW-Authenticate: Basic realm="1C Exchange"' );
                        header( 'HTTP/1.1 401 Unauthorized' );
                        exit;
                }

                if ( $_SERVER['PHP_AUTH_USER'] !== $user || $_SERVER['PHP_AUTH_PW'] !== $pass ) {
                        header( 'HTTP/1.1 403 Forbidden' );
                        echo "failure\nНеверный пользователь или пароль.";
                        $this->logger->error( 'Попытка доступа с неверными учётными данными', array( 'user' => $_SERVER['PHP_AUTH_USER'] ) );
                        exit;
                }

                $type = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';
                $mode = isset( $_GET['mode'] ) ? sanitize_text_field( $_GET['mode'] ) : '';

                $this->logger->info( 'Запрос обмена', array( 'type' => $type, 'mode' => $mode ) );

                switch ( $type ) {
                        case 'catalog':
                                $this->handle_catalog( $mode );
                                break;
                        case 'sale':
                                $this->handle_sale( $mode );
                                break;
                        default:
                                header( 'Content-Type: text/plain; charset=utf-8' );
                                echo "failure\nНеизвестный тип обмена: " . esc_html( $type );
                                exit;
                }
        }

        private function handle_catalog( $mode ) {
                header( 'Content-Type: text/plain; charset=utf-8' );

                switch ( $mode ) {
                        case 'checkauth':
                                $cookie_name  = 'wc1c_session';
                                $cookie_value = wp_generate_password( 32, false );
                                setcookie( $cookie_name, $cookie_value, time() + 3600, '/' );
                                echo "success\n";
                                echo $cookie_name . "\n";
                                echo $cookie_value . "\n";
                                $this->logger->info( 'Авторизация обмена успешна' );
                                break;

                        case 'init':
                                $file_limit = (int) get_option( 'wc1c_file_size_limit', 0 );
                                $time_limit = (int) get_option( 'wc1c_time_limit', 0 );
                                $use_zip    = get_option( 'wc1c_use_zip', '1' );
                                echo "zip=" . ( $use_zip === '1' ? 'yes' : 'no' ) . "\n";
                                echo "file_limit=" . $file_limit . "\n";
                                echo "time_limit=" . $time_limit . "\n";
                                $this->logger->info( 'Инициализация обмена', array( 'zip' => $use_zip, 'file_limit' => $file_limit ) );
                                break;

                        case 'file':
                                $this->receive_file();
                                break;

                        case 'import':
                                $filename = isset( $_GET['filename'] ) ? sanitize_file_name( $_GET['filename'] ) : '';
                                $this->import_file( $filename );
                                break;

                        default:
                                echo "failure\nНеизвестный режим каталога: " . esc_html( $mode );
                }
                exit;
        }

        private function handle_sale( $mode ) {
                header( 'Content-Type: text/plain; charset=utf-8' );

                switch ( $mode ) {
                        case 'checkauth':
                                $cookie_name  = 'wc1c_session';
                                $cookie_value = wp_generate_password( 32, false );
                                setcookie( $cookie_name, $cookie_value, time() + 3600, '/' );
                                echo "success\n";
                                echo $cookie_name . "\n";
                                echo $cookie_value . "\n";
                                break;

                        case 'init':
                                echo "zip=no\n";
                                echo "file_limit=0\n";
                                break;

                        case 'query':
                                $this->export_orders();
                                break;

                        case 'success':
                                $this->logger->success( 'Обмен заказами завершён успешно' );
                                echo "success\n";
                                break;

                        case 'file':
                                $this->receive_order_status();
                                break;

                        default:
                                echo "failure\nНеизвестный режим продаж: " . esc_html( $mode );
                }
                exit;
        }

        private function receive_file() {
                $upload_dir = wp_upload_dir();
                $exchange_dir = $upload_dir['basedir'] . '/wc1c/';

                if ( ! file_exists( $exchange_dir ) ) {
                        wp_mkdir_p( $exchange_dir );
                }

                $filename = isset( $_GET['filename'] ) ? sanitize_file_name( $_GET['filename'] ) : '';
                if ( empty( $filename ) ) {
                        echo "failure\nНе указано имя файла.";
                        exit;
                }

                $filepath = $exchange_dir . $filename;
                $data     = file_get_contents( 'php://input' );

                if ( false === $data ) {
                        echo "failure\nОшибка чтения данных.";
                        exit;
                }

                $flags = isset( $_GET['part'] ) && (int) $_GET['part'] > 1 ? FILE_APPEND : 0;
                file_put_contents( $filepath, $data, $flags );
                $this->logger->info( 'Получен файл', array( 'filename' => $filename, 'size' => strlen( $data ) ) );
                echo "success\n";
        }

        private function import_file( $filename ) {
                if ( empty( $filename ) ) {
                        echo "failure\nНе указан файл для импорта.";
                        exit;
                }

                $upload_dir   = wp_upload_dir();
                $exchange_dir = $upload_dir['basedir'] . '/wc1c/';
                $filepath     = $exchange_dir . $filename;

                if ( ! file_exists( $filepath ) ) {
                        echo "failure\nФайл не найден: " . esc_html( $filename );
                        exit;
                }

                $ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

                if ( $ext === 'zip' ) {
                        $result = $this->extract_zip( $filepath, $exchange_dir );
                        if ( ! $result ) {
                                echo "failure\nОшибка распаковки архива.";
                                exit;
                        }
                }

                if ( strpos( $filename, 'import' ) === 0 && $ext === 'xml' ) {
                        $importer = WC1C_Importer::get_instance();
                        $result   = $importer->process_catalog_xml( $filepath );
                        if ( $result['success'] ) {
                                $this->logger->success( 'Импорт каталога завершён', $result );
                                echo "success\n";
                                echo $result['message'] . "\n";
                        } else {
                                $this->logger->error( 'Ошибка импорта', $result );
                                echo "failure\n" . $result['message'] . "\n";
                        }
                } elseif ( strpos( $filename, 'offers' ) === 0 && $ext === 'xml' ) {
                        $importer = WC1C_Importer::get_instance();
                        $result   = $importer->process_offers_xml( $filepath );
                        if ( $result['success'] ) {
                                $this->logger->success( 'Импорт предложений завершён', $result );
                                echo "success\n";
                                echo $result['message'] . "\n";
                        } else {
                                $this->logger->error( 'Ошибка импорта предложений', $result );
                                echo "failure\n" . $result['message'] . "\n";
                        }
                } else {
                        echo "success\n";
                }
        }

        private function extract_zip( $filepath, $dest ) {
                if ( class_exists( 'ZipArchive' ) ) {
                        $zip = new ZipArchive();
                        if ( $zip->open( $filepath ) === true ) {
                                $zip->extractTo( $dest );
                                $zip->close();
                                return true;
                        }
                }
                return false;
        }

        private function export_orders() {
                if ( get_option( 'wc1c_export_orders', '0' ) !== '1' ) {
                        echo "success\n";
                        exit;
                }

                $statuses = get_option( 'wc1c_export_order_statuses', array( 'processing', 'on-hold' ) );
                if ( ! is_array( $statuses ) || empty( $statuses ) ) {
                        $statuses = array( 'processing' );
                }

                $wc_statuses = array_map( function( $s ) {
                        return 'wc-' . ltrim( $s, 'wc-' );
                }, $statuses );

                $orders = wc_get_orders( array(
                        'status'   => $wc_statuses,
                        'limit'    => 100,
                        'orderby'  => 'date',
                        'order'    => 'ASC',
                ) );

                if ( empty( $orders ) ) {
                        echo "success\n";
                        exit;
                }

                header( 'Content-Type: text/xml; charset=utf-8' );

                $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                $xml .= '<КоммерческаяИнформация ВерсияСхемы="2.09" ДатаФормирования="' . date( 'Y-m-d\TH:i:s' ) . '">' . "\n";
                $xml .= '<Документ>' . "\n";

                foreach ( $orders as $order ) {
                        $xml .= '<Заказ>' . "\n";
                        $xml .= '<Ид>' . esc_xml( $order->get_id() ) . '</Ид>' . "\n";
                        $xml .= '<Номер>' . esc_xml( $order->get_order_number() ) . '</Номер>' . "\n";
                        $xml .= '<Дата>' . esc_xml( $order->get_date_created()->format( 'Y-m-d' ) ) . '</Дата>' . "\n";
                        $xml .= '<ХозОперация>Заказ товара</ХозОперация>' . "\n";
                        $xml .= '<Роль>Продавец</Роль>' . "\n";
                        $xml .= '<Сумма>' . esc_xml( $order->get_total() ) . '</Сумма>' . "\n";
                        $xml .= '<Валюта>' . esc_xml( get_woocommerce_currency() ) . '</Валюта>' . "\n";

                        $xml .= '<Контрагент>' . "\n";
                        $xml .= '<Ид>' . esc_xml( $order->get_billing_email() ) . '</Ид>' . "\n";
                        $xml .= '<Наименование>' . esc_xml( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) . '</Наименование>' . "\n";
                        $xml .= '<Роль>Покупатель</Роль>' . "\n";
                        $xml .= '</Контрагент>' . "\n";

                        $xml .= '<Товары>' . "\n";
                        foreach ( $order->get_items() as $item ) {
                                $product = $item->get_product();
                                $xml .= '<Товар>' . "\n";
                                $xml .= '<Ид>' . esc_xml( $product ? $product->get_sku() : $item->get_product_id() ) . '</Ид>' . "\n";
                                $xml .= '<Наименование>' . esc_xml( $item->get_name() ) . '</Наименование>' . "\n";
                                $xml .= '<Количество>' . esc_xml( $item->get_quantity() ) . '</Количество>' . "\n";
                                $qty = max( 1, (int) $item->get_quantity() );
                                $xml .= '<ЦенаЗаЕдиницу>' . esc_xml( round( $item->get_subtotal() / $qty, 4 ) ) . '</ЦенаЗаЕдиницу>' . "\n";
                                $xml .= '<Сумма>' . esc_xml( $item->get_subtotal() ) . '</Сумма>' . "\n";
                                $xml .= '</Товар>' . "\n";
                        }
                        $xml .= '</Товары>' . "\n";
                        $xml .= '</Заказ>' . "\n";
                }

                $xml .= '</Документ>' . "\n";
                $xml .= '</КоммерческаяИнформация>';

                echo $xml;
                $this->logger->info( 'Выгружено заказов', array( 'count' => count( $orders ) ) );
        }

        private function receive_order_status() {
                header( 'Content-Type: text/plain; charset=utf-8' );
                $data = file_get_contents( 'php://input' );
                if ( empty( $data ) ) {
                        echo "success\n";
                        exit;
                }

                $xml = simplexml_load_string( $data );
                if ( ! $xml ) {
                        echo "failure\nОшибка разбора XML статусов заказов.";
                        exit;
                }

                echo "success\n";
        }

        public function test_connection() {
                $user    = get_option( 'wc1c_exchange_user', '' );
                $pass    = get_option( 'wc1c_exchange_pass', '' );
                $enabled = get_option( 'wc1c_enable_exchange', '0' );

                if ( $enabled !== '1' ) {
                        return array(
                                'success' => false,
                                'message' => 'Обмен не включён. Сначала включите обмен в <a href="' . esc_url( admin_url( 'admin.php?page=wc1c-settings' ) ) . '">Настройках</a>.',
                        );
                }

                if ( empty( $user ) || empty( $pass ) ) {
                        return array(
                                'success' => false,
                                'message' => 'Не заполнены поля «Пользователь» и «Пароль» обмена в <a href="' . esc_url( admin_url( 'admin.php?page=wc1c-settings' ) ) . '">Настройках</a>.',
                        );
                }

                $exchange_url = home_url( '/?wc1c_exchange=1&type=catalog&mode=checkauth' );
                $response     = wp_remote_get(
                        $exchange_url,
                        array(
                                'timeout' => 10,
                                'headers' => array(
                                        'Authorization' => 'Basic ' . base64_encode( $user . ':' . $pass ),
                                ),
                        )
                );

                if ( is_wp_error( $response ) ) {
                        return array(
                                'success' => false,
                                'message' => 'Ошибка соединения: ' . $response->get_error_message(),
                        );
                }

                $body = wp_remote_retrieve_body( $response );
                $code = wp_remote_retrieve_response_code( $response );

                if ( $code === 200 && strpos( $body, 'success' ) === 0 ) {
                        return array(
                                'success' => true,
                                'message' => 'Соединение успешно! URL обмена работает корректно.',
                                'url'     => $exchange_url,
                        );
                }

                return array(
                        'success' => false,
                        'message' => 'Неожиданный ответ сервера (код ' . $code . '): ' . substr( $body, 0, 200 ),
                );
        }
}
