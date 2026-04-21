<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap wc1c-wrap">
	<div class="wc1c-header">
		<div class="wc1c-header__logo">
			<span class="dashicons dashicons-networking"></span>
		</div>
		<div class="wc1c-header__info">
			<h1>Каталог товаров (Номенклатура)</h1>
			<p class="wc1c-header__subtitle">Настройки синхронизации товарного каталога</p>
		</div>
	</div>

	<div class="wc1c-catalog-tabs">
		<div class="wc1c-tab-nav">
			<button class="wc1c-tab-btn active" data-tab="tab-general">Основные</button>
			<button class="wc1c-tab-btn" data-tab="tab-products">Для товаров</button>
			<button class="wc1c-tab-btn" data-tab="tab-attributes">Для атрибутов</button>
			<button class="wc1c-tab-btn" data-tab="tab-images">Для изображений</button>
			<button class="wc1c-tab-btn" data-tab="tab-categories">Для категорий</button>
			<button class="wc1c-tab-btn" data-tab="tab-offers">Для предложений</button>
			<button class="wc1c-tab-btn" data-tab="tab-skip">Пропуск / исключение данных</button>
		</div>

		<div class="wc1c-tab-content active" id="tab-general">
			<div class="wc1c-card">
				<h3>Общие параметры синхронизации</h3>
				<table class="form-table wc1c-table">
					<tr>
						<th>URL обмена для 1С</th>
						<td>
							<code><?php echo esc_html( home_url( '/?wc1c_exchange=1' ) ); ?></code>
							<button class="button button-secondary wc1c-copy-btn" data-copy="<?php echo esc_attr( home_url( '/?wc1c_exchange=1' ) ); ?>">Скопировать</button>
							<p class="description">Укажите этот адрес в настройках узла обмена в 1С.</p>
						</td>
					</tr>
					<tr>
						<th>Пользователь обмена</th>
						<td><code><?php echo esc_html( get_option( 'wc1c_exchange_user', '(не задан)' ) ); ?></code></td>
					</tr>
					<tr>
						<th>Обмен включён</th>
						<td>
							<?php if ( get_option( 'wc1c_enable_exchange', '0' ) === '1' ) : ?>
								<span class="wc1c-badge wc1c-badge--success">Да</span>
							<?php else : ?>
								<span class="wc1c-badge wc1c-badge--error">Нет</span>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc1c-settings' ) ); ?>" class="button button-secondary">Включить в настройках</a>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th>ZIP-архив</th>
						<td>
							<?php if ( get_option( 'wc1c_use_zip', '1' ) === '1' ) : ?>
								<span class="wc1c-badge wc1c-badge--success">Включён</span>
							<?php else : ?>
								<span class="wc1c-badge wc1c-badge--warning">Отключён</span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th>Поддержка ZIP в PHP</th>
						<td>
							<?php if ( class_exists( 'ZipArchive' ) ) : ?>
								<span class="wc1c-badge wc1c-badge--success">Доступна</span>
							<?php else : ?>
								<span class="wc1c-badge wc1c-badge--error">Недоступна</span>
								<p class="description">Расширение ZipArchive не установлено. Обратитесь к хостинг-провайдеру.</p>
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="wc1c-tab-content" id="tab-products">
			<div class="wc1c-card">
				<h3>Настройки синхронизации товаров</h3>
				<table class="form-table wc1c-table">
					<tr>
						<th>Обновлять название</th>
						<td>
							<span class="wc1c-badge <?php echo get_option('wc1c_update_name','1')==='1'?'wc1c-badge--success':'wc1c-badge--error'; ?>">
								<?php echo get_option('wc1c_update_name','1')==='1'?'Да':'Нет'; ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>Обновлять описание</th>
						<td>
							<span class="wc1c-badge <?php echo get_option('wc1c_update_description','1')==='1'?'wc1c-badge--success':'wc1c-badge--error'; ?>">
								<?php echo get_option('wc1c_update_description','1')==='1'?'Да':'Нет'; ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>Обновлять артикул (SKU)</th>
						<td>
							<span class="wc1c-badge <?php echo get_option('wc1c_update_sku','1')==='1'?'wc1c-badge--success':'wc1c-badge--error'; ?>">
								<?php echo get_option('wc1c_update_sku','1')==='1'?'Да':'Нет'; ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>Поиск по артикулу</th>
						<td>
							<span class="wc1c-badge <?php echo get_option('wc1c_search_by_sku','0')==='1'?'wc1c-badge--success':'wc1c-badge--warning'; ?>">
								<?php echo get_option('wc1c_search_by_sku','0')==='1'?'Включён':'Отключён'; ?>
							</span>
						</td>
					</tr>
				</table>
				<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc1c-settings' ) ); ?>" class="button">Изменить настройки</a></p>
			</div>
		</div>

		<div class="wc1c-tab-content" id="tab-attributes">
			<div class="wc1c-card">
				<h3>Настройки атрибутов товаров</h3>
				<table class="form-table wc1c-table">
					<tr>
						<th>Обновлять атрибуты</th>
						<td>
							<span class="wc1c-badge <?php echo get_option('wc1c_update_attributes','1')==='1'?'wc1c-badge--success':'wc1c-badge--error'; ?>">
								<?php echo get_option('wc1c_update_attributes','1')==='1'?'Да':'Нет'; ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>Как хранятся атрибуты</th>
						<td>
							<p class="description">Атрибуты из 1С (свойства номенклатуры) записываются как атрибуты WooCommerce. 
							Название свойства из 1С становится именем атрибута. Значения — опциями атрибута.</p>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="wc1c-tab-content" id="tab-images">
			<div class="wc1c-card">
				<h3>Настройки изображений</h3>
				<table class="form-table wc1c-table">
					<tr>
						<th>Обновлять изображения</th>
						<td>
							<span class="wc1c-badge <?php echo get_option('wc1c_update_images','1')==='1'?'wc1c-badge--success':'wc1c-badge--error'; ?>">
								<?php echo get_option('wc1c_update_images','1')==='1'?'Да':'Нет'; ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>Загрузка изображений</th>
						<td>
							<p class="description">Изображения из 1С загружаются вместе с файлами выгрузки и прикрепляются к товарам. 
							Поддерживается ZIP-архивация для ускорения передачи.</p>
							<p class="description">Папка загрузки: <code><?php $ud = wp_upload_dir(); echo esc_html( $ud['basedir'] . '/wc1c/' ); ?></code></p>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="wc1c-tab-content" id="tab-categories">
			<div class="wc1c-card">
				<h3>Настройки категорий</h3>
				<table class="form-table wc1c-table">
					<tr>
						<th>Обновлять категории</th>
						<td>
							<span class="wc1c-badge <?php echo get_option('wc1c_update_categories','1')==='1'?'wc1c-badge--success':'wc1c-badge--error'; ?>">
								<?php echo get_option('wc1c_update_categories','1')==='1'?'Да':'Нет'; ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>Поиск категории по названию</th>
						<td>
							<span class="wc1c-badge <?php echo get_option('wc1c_find_category_by_name','0')==='1'?'wc1c-badge--success':'wc1c-badge--warning'; ?>">
								<?php echo get_option('wc1c_find_category_by_name','0')==='1'?'Включён':'Отключён'; ?>
							</span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="wc1c-tab-content" id="tab-offers">
			<div class="wc1c-card">
				<h3>Настройки предложений (цены и остатки)</h3>
				<table class="form-table wc1c-table">
					<tr>
						<th>Обновлять цены</th>
						<td>
							<span class="wc1c-badge <?php echo get_option('wc1c_update_prices','1')==='1'?'wc1c-badge--success':'wc1c-badge--error'; ?>">
								<?php echo get_option('wc1c_update_prices','1')==='1'?'Да':'Нет'; ?>
							</span>
						</td>
					</tr>
					<tr>
						<th>Тип базовой цены</th>
						<td>
							<?php $bpt = get_option('wc1c_base_price_type',''); ?>
							<code><?php echo $bpt ? esc_html($bpt) : '(первый из выгрузки)'; ?></code>
						</td>
					</tr>
					<tr>
						<th>Тип цены распродажи</th>
						<td>
							<?php $spt = get_option('wc1c_sale_price_type',''); ?>
							<code><?php echo $spt ? esc_html($spt) : '(не задан)'; ?></code>
						</td>
					</tr>
					<tr>
						<th>Обновлять остатки</th>
						<td>
							<span class="wc1c-badge <?php echo get_option('wc1c_update_stock','1')==='1'?'wc1c-badge--success':'wc1c-badge--error'; ?>">
								<?php echo get_option('wc1c_update_stock','1')==='1'?'Да':'Нет'; ?>
							</span>
						</td>
					</tr>
					<?php $price_types = get_option('wc1c_price_types', array()); ?>
					<?php if ( ! empty( $price_types ) ) : ?>
					<tr>
						<th>Типы цен (из 1С)</th>
						<td>
							<?php foreach ( $price_types as $id => $name ) : ?>
								<div><code><?php echo esc_html($id); ?></code> — <?php echo esc_html($name); ?></div>
							<?php endforeach; ?>
							<p class="description">Типы цен, полученные при последнем обмене. Используйте ID в полях выше.</p>
						</td>
					</tr>
					<?php endif; ?>
				</table>
				<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc1c-settings' ) ); ?>" class="button">Изменить настройки</a></p>
			</div>
		</div>

		<div class="wc1c-tab-content" id="tab-skip">
			<div class="wc1c-card">
				<h3>Пропуск и исключение данных</h3>
				<p>Управляйте тем, какие данные не нужно обновлять при синхронизации. Снимите галочки в <a href="<?php echo esc_url( admin_url( 'admin.php?page=wc1c-settings' ) ); ?>">настройках</a> для нужных полей.</p>
				<table class="form-table wc1c-table">
					<thead>
						<tr>
							<th>Поле</th>
							<th>Статус</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$fields = array(
							'wc1c_update_name'           => 'Название товара',
							'wc1c_update_description'    => 'Описание товара',
							'wc1c_update_sku'            => 'Артикул (SKU)',
							'wc1c_update_categories'     => 'Категории',
							'wc1c_update_attributes'     => 'Атрибуты',
							'wc1c_update_images'         => 'Изображения',
							'wc1c_update_prices'         => 'Цены',
							'wc1c_update_stock'          => 'Остатки',
						);
						foreach ( $fields as $key => $label ) :
							$active = get_option( $key, '1' ) === '1';
						?>
						<tr>
							<td><?php echo esc_html($label); ?></td>
							<td>
								<span class="wc1c-badge <?php echo $active ? 'wc1c-badge--success' : 'wc1c-badge--error'; ?>">
									<?php echo $active ? 'Обновляется' : 'Пропускается'; ?>
								</span>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
