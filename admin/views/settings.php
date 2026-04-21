<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap wc1c-wrap">
	<div class="wc1c-header">
		<div class="wc1c-header__logo">
			<span class="dashicons dashicons-networking"></span>
		</div>
		<div class="wc1c-header__info">
			<h1>Настройки обмена 1С</h1>
			<p class="wc1c-header__subtitle">Интеграция WooCommerce 1С:Предприятие</p>
		</div>
	</div>

	<?php if ( $notice ) : ?>
		<div class="wc1c-notice wc1c-notice--<?php echo esc_attr( $notice['type'] ); ?>">
			<?php echo wp_kses_post( $notice['message'] ); ?>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php wp_nonce_field( 'wc1c_save_settings' ); ?>
		<input type="hidden" name="action" value="wc1c_save_settings">

		<div class="wc1c-settings-grid">

			<div class="wc1c-card">
				<h2><span class="dashicons dashicons-admin-links"></span> Основные настройки</h2>

				<table class="form-table wc1c-table">
					<tr>
						<th>Включить обмен</th>
						<td>
							<label class="wc1c-toggle">
								<input type="checkbox" name="wc1c_enable_exchange" value="1" <?php checked( get_option( 'wc1c_enable_exchange', '0' ), '1' ); ?>>
								<span class="wc1c-toggle__slider"></span>
							</label>
							<p class="description">Включить endpoint для приёма данных от 1С/СБИС/МойСклад.</p>
						</td>
					</tr>
					<tr>
						<th>Пользователь</th>
						<td>
							<input type="text" name="wc1c_exchange_user" class="regular-text" value="<?php echo esc_attr( get_option( 'wc1c_exchange_user', '' ) ); ?>" placeholder="1c_user" autocomplete="off">
							<p class="description">Логин для авторизации обмена данными из 1С.</p>
						</td>
					</tr>
					<tr>
						<th>Пароль</th>
						<td>
							<input type="password" name="wc1c_exchange_pass" class="regular-text" value="<?php echo esc_attr( get_option( 'wc1c_exchange_pass', '' ) ); ?>" autocomplete="new-password">
							<p class="description">Пароль для авторизации обмена данными из 1С.</p>
						</td>
					</tr>
					<tr>
						<th>URL обмена</th>
						<td>
							<code><?php echo esc_html( home_url( '/?wc1c_exchange=1' ) ); ?></code>
							<p class="description">Используйте этот URL при настройке узла обмена в 1С.</p>
						</td>
					</tr>
				</table>
			</div>

			<div class="wc1c-card">
				<h2><span class="dashicons dashicons-performance"></span> Производительность</h2>

				<table class="form-table wc1c-table">
					<tr>
						<th>Размер части файла (Мб)</th>
						<td>
							<input type="number" name="wc1c_file_size_limit" class="small-text" min="0" value="<?php echo esc_attr( get_option( 'wc1c_file_size_limit', '0' ) ); ?>">
							<p class="description">0 = без ограничений. Используйте если хостинг ограничивает размер загружаемых файлов.</p>
						</td>
					</tr>
					<tr>
						<th>Лимит времени работы (сек)</th>
						<td>
							<input type="number" name="wc1c_time_limit" class="small-text" min="0" value="<?php echo esc_attr( get_option( 'wc1c_time_limit', '0' ) ); ?>">
							<p class="description">0 = без ограничений. Время на выполнение одного шага импорта.</p>
						</td>
					</tr>
					<tr>
						<th>Обмен в архиве (zip)</th>
						<td>
							<label class="wc1c-toggle">
								<input type="checkbox" name="wc1c_use_zip" value="1" <?php checked( get_option( 'wc1c_use_zip', '1' ), '1' ); ?>>
								<span class="wc1c-toggle__slider"></span>
							</label>
							<p class="description">Рекомендуется включить — ускоряет передачу данных.</p>
						</td>
					</tr>
				</table>
			</div>

			<div class="wc1c-card">
				<h2><span class="dashicons dashicons-products"></span> Для товаров</h2>

				<table class="form-table wc1c-table">
					<tr>
						<th>Обновлять название</th>
						<td><label class="wc1c-toggle"><input type="checkbox" name="wc1c_update_name" value="1" <?php checked( get_option( 'wc1c_update_name', '1' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label></td>
					</tr>
					<tr>
						<th>Обновлять описание</th>
						<td><label class="wc1c-toggle"><input type="checkbox" name="wc1c_update_description" value="1" <?php checked( get_option( 'wc1c_update_description', '1' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label></td>
					</tr>
					<tr>
						<th>Обновлять артикул (SKU)</th>
						<td><label class="wc1c-toggle"><input type="checkbox" name="wc1c_update_sku" value="1" <?php checked( get_option( 'wc1c_update_sku', '1' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label></td>
					</tr>
					<tr>
						<th>Обновлять изображения</th>
						<td><label class="wc1c-toggle"><input type="checkbox" name="wc1c_update_images" value="1" <?php checked( get_option( 'wc1c_update_images', '1' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label></td>
					</tr>
					<tr>
						<th>Поиск товара по артикулу</th>
						<td>
							<label class="wc1c-toggle"><input type="checkbox" name="wc1c_search_by_sku" value="1" <?php checked( get_option( 'wc1c_search_by_sku', '0' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label>
							<p class="description">Искать существующий товар по артикулу вместо создания нового.</p>
						</td>
					</tr>
				</table>
			</div>

			<div class="wc1c-card">
				<h2><span class="dashicons dashicons-tag"></span> Для категорий и атрибутов</h2>

				<table class="form-table wc1c-table">
					<tr>
						<th>Обновлять категории</th>
						<td><label class="wc1c-toggle"><input type="checkbox" name="wc1c_update_categories" value="1" <?php checked( get_option( 'wc1c_update_categories', '1' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label></td>
					</tr>
					<tr>
						<th>Обновлять атрибуты</th>
						<td><label class="wc1c-toggle"><input type="checkbox" name="wc1c_update_attributes" value="1" <?php checked( get_option( 'wc1c_update_attributes', '1' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label></td>
					</tr>
					<tr>
						<th>Поиск категории по названию</th>
						<td>
							<label class="wc1c-toggle"><input type="checkbox" name="wc1c_find_category_by_name" value="1" <?php checked( get_option( 'wc1c_find_category_by_name', '0' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label>
							<p class="description">Привязывать существующие категории по названию, а не создавать новые.</p>
						</td>
					</tr>
				</table>
			</div>

			<div class="wc1c-card">
				<h2><span class="dashicons dashicons-cart"></span> Для предложений (цены и остатки)</h2>

				<table class="form-table wc1c-table">
					<tr>
						<th>Обновлять цены</th>
						<td><label class="wc1c-toggle"><input type="checkbox" name="wc1c_update_prices" value="1" <?php checked( get_option( 'wc1c_update_prices', '1' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label></td>
					</tr>
					<tr>
						<th>Тип базовой цены</th>
						<td>
							<input type="text" name="wc1c_base_price_type" class="regular-text" value="<?php echo esc_attr( get_option( 'wc1c_base_price_type', '' ) ); ?>" placeholder="(заполнится автоматически при первом обмене)">
							<p class="description">ID типа цены из 1С для основной цены товара.</p>
						</td>
					</tr>
					<tr>
						<th>Тип цены распродажи</th>
						<td>
							<input type="text" name="wc1c_sale_price_type" class="regular-text" value="<?php echo esc_attr( get_option( 'wc1c_sale_price_type', '' ) ); ?>" placeholder="(не обязательно)">
							<p class="description">ID типа цены из 1С для цены по скидке.</p>
						</td>
					</tr>
					<tr>
						<th>Обновлять остатки</th>
						<td><label class="wc1c-toggle"><input type="checkbox" name="wc1c_update_stock" value="1" <?php checked( get_option( 'wc1c_update_stock', '1' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label></td>
					</tr>
				</table>
			</div>

			<div class="wc1c-card">
				<h2><span class="dashicons dashicons-store"></span> Выгрузка заказов</h2>

				<table class="form-table wc1c-table">
					<tr>
						<th>Включить выгрузку заказов</th>
						<td>
							<label class="wc1c-toggle"><input type="checkbox" name="wc1c_export_orders" value="1" <?php checked( get_option( 'wc1c_export_orders', '0' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label>
							<p class="description">Передавать заказы с сайта в 1С при обмене.</p>
						</td>
					</tr>
				</table>
			</div>

			<div class="wc1c-card">
				<h2><span class="dashicons dashicons-list-view"></span> Логирование</h2>

				<table class="form-table wc1c-table">
					<tr>
						<th>Вести лог обмена</th>
						<td>
							<label class="wc1c-toggle"><input type="checkbox" name="wc1c_enable_logging" value="1" <?php checked( get_option( 'wc1c_enable_logging', '1' ), '1' ); ?>><span class="wc1c-toggle__slider"></span></label>
							<p class="description">Записывать события обмена в лог для отладки.</p>
						</td>
					</tr>
				</table>
			</div>

		</div>

		<p class="submit">
			<button type="submit" class="button button-primary button-hero">
				<span class="dashicons dashicons-saved" style="margin-top:4px"></span> Сохранить настройки
			</button>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc1c-test' ) ); ?>" class="button button-secondary button-hero">
				<span class="dashicons dashicons-yes" style="margin-top:4px"></span> Тест подключения
			</a>
		</p>
	</form>

	<div class="wc1c-footer">
		<p>Разработчик: <a href="https://рукодер.рф/" target="_blank">Сергей Солошенко (РуКодер)</a> | 
		<a href="mailto:support@рукодер.рф">support@рукодер.рф</a> | 
		<a href="https://t.me/RussCoder" target="_blank">Telegram: @RussCoder</a></p>
	</div>
</div>
