<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap wc1c-wrap">
	<div class="wc1c-header">
		<div class="wc1c-header__logo">
			<span class="dashicons dashicons-networking"></span>
		</div>
		<div class="wc1c-header__info">
			<h1>Интеграция WooCommerce 1С:Предприятие</h1>
			<p class="wc1c-header__subtitle">1С-Обмен для WooCommerce: интеграция с 1С, СБИС, МойСклад</p>
		</div>
	</div>

	<?php if ( $notice ) : ?>
		<div class="wc1c-notice wc1c-notice--<?php echo esc_attr( $notice['type'] ); ?>">
			<?php echo wp_kses_post( $notice['message'] ); ?>
		</div>
	<?php endif; ?>

	<?php $license = WC1C_License::get_instance(); ?>

	<?php if ( $license->is_active() ) : ?>
		<div class="wc1c-card wc1c-card--success">
			<div class="wc1c-card__icon"><span class="dashicons dashicons-yes-alt"></span></div>
			<div class="wc1c-card__body">
				<h2>Плагин активирован</h2>
				<p>Лицензионный ключ: <code><?php echo esc_html( $license->get_key() ); ?></code></p>
				<p>Все функции плагина доступны. Перейдите в <a href="<?php echo esc_url( admin_url( 'admin.php?page=wc1c-settings' ) ); ?>">Настройки</a> для настройки обмена.</p>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:16px">
					<?php wp_nonce_field( 'wc1c_deactivate_license' ); ?>
					<input type="hidden" name="action" value="wc1c_deactivate_license">
					<button type="submit" class="button button-secondary" onclick="return confirm('Вы уверены, что хотите деактивировать лицензию? Доступ к функциям будет ограничен.')">
						Деактивировать лицензию
					</button>
				</form>
			</div>
		</div>
	<?php else : ?>

		<div class="wc1c-activation-grid">

			<div class="wc1c-card">
				<h2><span class="dashicons dashicons-admin-network"></span> Активация плагина</h2>
				<p>Для использования всех функций <strong>1C-Integration-WooCommerce</strong> введите лицензионный ключ.<br>
				Если у вас нет ключа — запросите его ниже.</p>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wc1c-form">
					<?php wp_nonce_field( 'wc1c_activate_license' ); ?>
					<input type="hidden" name="action" value="wc1c_activate_license">

					<div class="wc1c-form__row">
						<label for="license_key">Лицензионный ключ</label>
						<input type="text"
							id="license_key"
							name="license_key"
							value=""
							placeholder="WC1C-XXXX-XXXX-XXXX-XXXX"
							class="regular-text"
							style="text-transform:uppercase"
						>
						<p class="description">
							Формат ключа: <code>WC1C-XXXX-XXXX-XXXX-XXXX</code>.
							Осталось попыток: <strong><?php echo esc_html( $license->get_attempts_left() ); ?> из <?php echo WC1C_MAX_ATTEMPTS; ?></strong>.
						</p>
					</div>

					<div class="wc1c-form__actions">
						<button type="submit" class="button button-primary button-hero">
							<span class="dashicons dashicons-lock" style="margin-top:4px"></span> Активировать
						</button>
					</div>
				</form>
			</div>

			<div class="wc1c-card">
				<h2><span class="dashicons dashicons-email-alt"></span> Запросить лицензионный ключ</h2>
				<p>Нет ключа? Заполните форму — владелец плагина рассмотрит запрос и пришлёт ключ на ваш email.</p>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wc1c-form">
					<?php wp_nonce_field( 'wc1c_request_license' ); ?>
					<input type="hidden" name="action" value="wc1c_request_license">

					<div class="wc1c-form__row">
						<label for="requester_name">Ваше имя <span class="required">*</span></label>
						<input type="text" id="requester_name" name="requester_name" class="regular-text" required placeholder="Иван Иванов">
					</div>

					<div class="wc1c-form__row">
						<label for="requester_email">Ваш email <span class="required">*</span></label>
						<input type="email" id="requester_email" name="requester_email" class="regular-text" required placeholder="ivan@example.com">
						<p class="description">Как с вами связаться? На этот адрес придёт лицензионный ключ.</p>
					</div>

					<div class="wc1c-form__row">
						<label for="requester_site">Ссылка на сайт</label>
						<input type="url" id="requester_site" name="requester_site" class="regular-text" placeholder="https://example.com" value="<?php echo esc_attr( home_url() ); ?>">
					</div>

					<div class="wc1c-form__row">
						<label for="requester_social">Ссылка на соц. сети / мессенджер</label>
						<input type="text" id="requester_social" name="requester_social" class="regular-text" placeholder="https://t.me/username или ссылка VK / Instagram">
					</div>

					<div class="wc1c-form__row">
						<label class="wc1c-checkbox-label">
							<input type="checkbox" name="consent_personal" value="1" required>
							Согласия <span class="required">*</span> — даю своё согласие на обработку персональных данных.
						</label>
					</div>

					<div class="wc1c-form__row">
						<label class="wc1c-checkbox-label">
							<input type="checkbox" name="consent_subscribe" value="1">
							Согласен(а) получать информационные рассылки и уведомления об акциях на указанный email. Подтверждаю, что могу отменить подписку в любое время.
						</label>
					</div>

					<div class="wc1c-form__actions">
						<button type="submit" class="button button-primary">
							<span class="dashicons dashicons-email" style="margin-top:4px"></span> Отправить запрос
						</button>
					</div>
				</form>
			</div>

		</div>

		<div class="wc1c-card wc1c-card--info">
			<h3><span class="dashicons dashicons-lock"></span> Что недоступно без лицензии?</h3>
			<ul class="wc1c-feature-list">
				<li><span class="dashicons dashicons-no"></span> Синхронизация товаров и категорий из 1С</li>
				<li><span class="dashicons dashicons-no"></span> Обновление цен и остатков</li>
				<li><span class="dashicons dashicons-no"></span> Выгрузка заказов в 1С</li>
				<li><span class="dashicons dashicons-no"></span> Настройки обмена</li>
				<li><span class="dashicons dashicons-no"></span> Логи и тест подключения</li>
			</ul>
		</div>

	<?php endif; ?>

	<div class="wc1c-footer">
		<p>Разработчик: <a href="https://рукодер.рф/" target="_blank">Сергей Солошенко (РуКодер)</a> |
		<a href="mailto:support@рукодер.рф">support@рукодер.рф</a> |
		<a href="https://t.me/RussCoder" target="_blank">Telegram: @RussCoder</a> |
		Версия: <?php echo WC1C_VERSION; ?></p>
	</div>
</div>
