<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WC1C_Importer {

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
        }

        public function process_catalog_xml( $filepath ) {
                $start_time = microtime( true );

                if ( ! file_exists( $filepath ) ) {
                        return array( 'success' => false, 'message' => 'Файл не найден: ' . basename( $filepath ) );
                }

                libxml_use_internal_errors( true );
                $xml = simplexml_load_file( $filepath, 'SimpleXMLElement', LIBXML_NOCDATA );

                if ( ! $xml ) {
                        $errors = libxml_get_errors();
                        $msg    = 'Ошибка разбора XML';
                        if ( ! empty( $errors ) ) {
                                $msg .= ': ' . $errors[0]->message;
                        }
                        return array( 'success' => false, 'message' => $msg );
                }

                $xml->registerXPathNamespace( 'ns', 'urn:1C.ru:commerceml_2' );

                $categories_count = 0;
                $products_count   = 0;
                $errors_count     = 0;

                if ( isset( $xml->Классификатор->Группы ) ) {
                        foreach ( $xml->Классификатор->Группы->Группа as $group ) {
                                $result = $this->process_category( $group, 0 );
                                if ( $result ) {
                                        $categories_count++;
                                }
                        }
                }

                $attributes_map = array();
                if ( isset( $xml->Классификатор->Свойства ) ) {
                        foreach ( $xml->Классификатор->Свойства->Свойство as $prop ) {
                                $prop_id   = (string) $prop->Ид;
                                $prop_name = (string) $prop->Наименование;
                                $attributes_map[ $prop_id ] = $prop_name;
                        }
                }

                if ( isset( $xml->Каталог->Товары ) ) {
                        foreach ( $xml->Каталог->Товары->Товар as $product_xml ) {
                                $result = $this->process_product( $product_xml, $attributes_map );
                                if ( $result ) {
                                        $products_count++;
                                } else {
                                        $errors_count++;
                                }
                        }
                }

                $duration = round( microtime( true ) - $start_time, 2 );
                $message  = "Категорий: {$categories_count}, Товаров: {$products_count}, Ошибок: {$errors_count}. Время: {$duration}с.";

                $this->logger->success( 'Импорт каталога', array(
                        'categories' => $categories_count,
                        'products'   => $products_count,
                        'errors'     => $errors_count,
                        'duration'   => $duration,
                ) );

                return array( 'success' => true, 'message' => $message );
        }

        public function process_offers_xml( $filepath ) {
                $start_time = microtime( true );

                if ( ! file_exists( $filepath ) ) {
                        return array( 'success' => false, 'message' => 'Файл не найден: ' . basename( $filepath ) );
                }

                libxml_use_internal_errors( true );
                $xml = simplexml_load_file( $filepath, 'SimpleXMLElement', LIBXML_NOCDATA );

                if ( ! $xml ) {
                        return array( 'success' => false, 'message' => 'Ошибка разбора XML предложений' );
                }

                $prices_count = 0;
                $stock_count  = 0;

                $price_types = array();
                if ( isset( $xml->ПакетПредложений->ТипыЦен ) ) {
                        foreach ( $xml->ПакетПредложений->ТипыЦен->ТипЦены as $price_type ) {
                                $pt_id   = (string) $price_type->Ид;
                                $pt_name = (string) $price_type->Наименование;
                                $price_types[ $pt_id ] = $pt_name;
                        }
                        if ( ! empty( $price_types ) && empty( get_option( 'wc1c_price_types' ) ) ) {
                                update_option( 'wc1c_price_types', $price_types );
                        }
                }

                $base_price_type    = get_option( 'wc1c_base_price_type', '' );
                $sale_price_type    = get_option( 'wc1c_sale_price_type', '' );
                $exclude_warehouses = get_option( 'wc1c_exclude_warehouses', array() );

                if ( isset( $xml->ПакетПредложений->Предложения ) ) {
                        foreach ( $xml->ПакетПредложений->Предложения->Предложение as $offer ) {
                                $sku    = (string) $offer->Ид;
                                $sku    = strpos( $sku, '#' ) !== false ? explode( '#', $sku )[0] : $sku;

                                $product_id = wc_get_product_id_by_sku( $sku );
                                if ( ! $product_id ) {
                                        $args = array(
                                                'post_type'  => 'product',
                                                'meta_query' => array(
                                                        array(
                                                                'key'   => '_wc1c_id',
                                                                'value' => $sku,
                                                        ),
                                                ),
                                        );
                                        $q    = new WP_Query( $args );
                                        if ( $q->have_posts() ) {
                                                $product_id = $q->posts[0]->ID;
                                        }
                                }

                                if ( ! $product_id ) {
                                        continue;
                                }

                                $product = wc_get_product( $product_id );
                                if ( ! $product ) {
                                        continue;
                                }

                                if ( isset( $offer->Цены ) && get_option( 'wc1c_update_prices', '1' ) === '1' ) {
                                        $base_price = null;
                                        $sale_price = null;

                                        foreach ( $offer->Цены->Цена as $price_xml ) {
                                                $pt_id = (string) $price_xml->ИдТипаЦены;
                                                $price = (float) str_replace( ',', '.', (string) $price_xml->ЦенаЗаЕдиницу );

                                                if ( empty( $base_price_type ) || $pt_id === $base_price_type ) {
                                                        if ( $base_price === null ) {
                                                                $base_price = $price;
                                                        }
                                                }
                                                if ( ! empty( $sale_price_type ) && $pt_id === $sale_price_type ) {
                                                        $sale_price = $price;
                                                }
                                        }

                                        if ( $base_price !== null ) {
                                                $product->set_regular_price( $base_price );
                                                $prices_count++;
                                        }
                                        if ( $sale_price !== null ) {
                                                $product->set_sale_price( $sale_price );
                                        }
                                }

                                if ( isset( $offer->Остатки ) && get_option( 'wc1c_update_stock', '1' ) === '1' ) {
                                        $total_stock = 0;
                                        foreach ( $offer->Остатки->Остаток as $stock_xml ) {
                                                $warehouse_id = (string) $stock_xml->Склад->Ид;
                                                if ( ! empty( $exclude_warehouses ) && in_array( $warehouse_id, (array) $exclude_warehouses ) ) {
                                                        continue;
                                                }
                                                $qty = (float) str_replace( ',', '.', (string) $stock_xml->Количество );
                                                $total_stock += $qty;
                                        }
                                        $product->set_manage_stock( true );
                                        $product->set_stock_quantity( $total_stock );
                                        $stock_count++;
                                }

                                $product->save();
                        }
                }

                $duration = round( microtime( true ) - $start_time, 2 );
                $message  = "Цен обновлено: {$prices_count}, Остатков обновлено: {$stock_count}. Время: {$duration}с.";

                $this->logger->success( 'Импорт предложений', array(
                        'prices' => $prices_count,
                        'stock'  => $stock_count,
                        'duration' => $duration,
                ) );

                return array( 'success' => true, 'message' => $message );
        }

        private function process_category( $group_xml, $parent_id ) {
                $group_id   = (string) $group_xml->Ид;
                $group_name = (string) $group_xml->Наименование;

                if ( empty( $group_id ) || empty( $group_name ) ) {
                        return false;
                }

                $existing_terms = get_terms( array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => false,
                        'meta_query' => array(
                                array(
                                        'key'   => '_wc1c_category_id',
                                        'value' => $group_id,
                                ),
                        ),
                ) );
                $existing_term = ( ! empty( $existing_terms ) && ! is_wp_error( $existing_terms ) ) ? $existing_terms[0] : null;

                if ( $existing_term ) {
                        if ( get_option( 'wc1c_update_categories', '1' ) === '1' ) {
                                wp_update_term( $existing_term->term_id, 'product_cat', array(
                                        'name'   => $group_name,
                                        'parent' => $parent_id,
                                ) );
                        }
                        $term_id = $existing_term->term_id;
                } else {
                        $find_by_name = get_option( 'wc1c_find_category_by_name', '0' );
                        if ( $find_by_name === '1' ) {
                                $args = array(
                                        'taxonomy'   => 'product_cat',
                                        'name'       => $group_name,
                                        'parent'     => $parent_id,
                                        'hide_empty' => false,
                                );
                                $existing = get_terms( $args );
                                if ( ! empty( $existing ) && ! is_wp_error( $existing ) ) {
                                        $term_id = $existing[0]->term_id;
                                        update_term_meta( $term_id, '_wc1c_category_id', $group_id );
                                }
                        }

                        if ( empty( $term_id ) ) {
                                $result = wp_insert_term( $group_name, 'product_cat', array( 'parent' => $parent_id ) );
                                if ( is_wp_error( $result ) ) {
                                        $this->logger->error( 'Ошибка создания категории', array( 'name' => $group_name, 'error' => $result->get_error_message() ) );
                                        return false;
                                }
                                $term_id = $result['term_id'];
                                update_term_meta( $term_id, '_wc1c_category_id', $group_id );
                        }
                }

                if ( isset( $group_xml->Группы ) ) {
                        foreach ( $group_xml->Группы->Группа as $child_group ) {
                                $this->process_category( $child_group, $term_id );
                        }
                }

                return true;
        }

        private function process_product( $product_xml, $attributes_map ) {
                $product_1c_id = (string) $product_xml->Ид;
                $product_name  = (string) $product_xml->Наименование;
                $product_sku   = (string) $product_xml->Артикул;
                $product_desc  = (string) $product_xml->Описание;

                if ( empty( $product_1c_id ) || empty( $product_name ) ) {
                        return false;
                }

                $product_id = null;

                $search_by_sku = get_option( 'wc1c_search_by_sku', '0' );
                if ( $search_by_sku === '1' && ! empty( $product_sku ) ) {
                        $product_id = wc_get_product_id_by_sku( $product_sku );
                }

                if ( ! $product_id ) {
                        $args = array(
                                'post_type'  => 'product',
                                'meta_query' => array(
                                        array(
                                                'key'   => '_wc1c_id',
                                                'value' => $product_1c_id,
                                        ),
                                ),
                                'fields'     => 'ids',
                                'posts_per_page' => 1,
                        );
                        $results = get_posts( $args );
                        if ( ! empty( $results ) ) {
                                $product_id = $results[0];
                        }
                }

                if ( $product_id ) {
                        $product = wc_get_product( $product_id );
                        if ( ! $product ) {
                                return false;
                        }
                } else {
                        $product = new WC_Product_Simple();
                }

                if ( get_option( 'wc1c_update_name', '1' ) === '1' ) {
                        $product->set_name( $product_name );
                }
                if ( get_option( 'wc1c_update_description', '1' ) === '1' && ! empty( $product_desc ) ) {
                        $product->set_description( $product_desc );
                }
                if ( ! empty( $product_sku ) && get_option( 'wc1c_update_sku', '1' ) === '1' ) {
                        $product->set_sku( $product_sku );
                }

                if ( ! empty( $product_xml->Группы ) && get_option( 'wc1c_update_categories', '1' ) === '1' ) {
                        $cat_ids = array();
                        foreach ( $product_xml->Группы->Ид as $cat_1c_id ) {
                                $terms = get_terms( array(
                                        'taxonomy'   => 'product_cat',
                                        'hide_empty' => false,
                                        'meta_query' => array(
                                                array(
                                                        'key'   => '_wc1c_category_id',
                                                        'value' => (string) $cat_1c_id,
                                                ),
                                        ),
                                ) );
                                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                                        $cat_ids[] = $terms[0]->term_id;
                                }
                        }
                        if ( ! empty( $cat_ids ) ) {
                                $product->set_category_ids( $cat_ids );
                        }
                }

                if ( ! empty( $product_xml->ЗначенияСвойств ) && get_option( 'wc1c_update_attributes', '1' ) === '1' ) {
                        $attributes = array();
                        foreach ( $product_xml->ЗначенияСвойств->ЗначениеСвойства as $prop_val ) {
                                $prop_id    = (string) $prop_val->Ид;
                                $prop_value = (string) $prop_val->Значение;
                                $prop_name  = isset( $attributes_map[ $prop_id ] ) ? $attributes_map[ $prop_id ] : $prop_id;

                                if ( empty( $prop_value ) ) {
                                        continue;
                                }

                                $attr_name = sanitize_title( $prop_name );
                                $taxonomy  = wc_attribute_taxonomy_name( $prop_name );

                                $attribute = new WC_Product_Attribute();
                                $attribute->set_name( $taxonomy );
                                $attribute->set_options( array( $prop_value ) );
                                $attribute->set_visible( true );
                                $attributes[] = $attribute;
                        }
                        if ( ! empty( $attributes ) ) {
                                $product->set_attributes( $attributes );
                        }
                }

                if ( ! empty( $product_xml->Картинка ) && get_option( 'wc1c_update_images', '1' ) === '1' ) {
                        $image_path = (string) $product_xml->Картинка;
                        $upload_dir = wp_upload_dir();
                        $full_path  = $upload_dir['basedir'] . '/wc1c/' . $image_path;

                        if ( file_exists( $full_path ) ) {
                                $current_image_id = $product->get_image_id();
                                $image_hash_key   = '_wc1c_image_hash';
                                $file_hash        = md5_file( $full_path );
                                $stored_hash      = $current_image_id ? get_post_meta( $product->get_id(), $image_hash_key, true ) : '';

                                if ( $file_hash !== $stored_hash ) {
                                        $image_id = $this->attach_image( $full_path, $product->get_id(), $product_name );
                                        if ( $image_id ) {
                                                $product->set_image_id( $image_id );
                                                update_post_meta( $product->get_id(), $image_hash_key, $file_hash );
                                        }
                                }
                        }
                }

                $product->save();
                $saved_id = $product->get_id();

                update_post_meta( $saved_id, '_wc1c_id', $product_1c_id );

                return true;
        }

        private function attach_image( $filepath, $product_id, $title = '' ) {
                $upload_dir = wp_upload_dir();
                $filename   = basename( $filepath );
                $dest_path  = $upload_dir['path'] . '/' . $filename;

                if ( ! copy( $filepath, $dest_path ) ) {
                        return false;
                }

                $filetype   = wp_check_filetype( $filename );
                $attachment = array(
                        'post_mime_type' => $filetype['type'],
                        'post_title'     => ! empty( $title ) ? $title : $filename,
                        'post_content'   => '',
                        'post_status'    => 'inherit',
                );

                $attach_id = wp_insert_attachment( $attachment, $dest_path, $product_id );
                if ( ! is_wp_error( $attach_id ) ) {
                        require_once( ABSPATH . 'wp-admin/includes/image.php' );
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $dest_path );
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                        return $attach_id;
                }

                return false;
        }
}
