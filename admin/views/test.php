<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap wc1c-wrap">
	<div class="wc1c-header">
		<div class="wc1c-header__logo">
			<span class="dashicons dashicons-networking"></span>
		</div>
		<div class="wc1c-header__info">
			<h1>Тест подключения</h1>
			<p class="wc1c-header__subtitle">Проверка соединения с сервером обмена 1С</p>
		</div>
	</div>

	<?php if ( $notice ) : ?>
		<div class="wc1c-notice wc1c-notice--<?php echo esc_attr( $notice['type'] ); ?>">
			<?php echo wp_kses_post( $notice['message'] ); ?>
		</div>
	<?php endif; ?>

	<div class="wc1c-settings-grid">

		<div class="wc1c-card">
			<h2><span class="dashicons dashicons-admin-tools"></span> Проверить соединение</h2>
			<p>Нажмите кнопку, чтобы проверить, что URL обмена работает корректно и учётные данные верны.</p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'wc1c_test_connection' ); ?>
				<input type="hidden" name="action" value="wc1c_test_connection">
				<p>
					<button type="submit" class="button button-primary button-hero">
						<span class="dashicons dashicons-yes" style="margin-top:4px"></span> Запустить тест
					</button>
				</p>
			</form>
		</div>

		<div class="wc1c-card">
			<h2><span class="dashicons dashicons-info"></span> Диагностика окружения</h2>
			<table class="form-table wc1c-table">
				<tr>
					<th>WordPress</th>
					<td><?php global $wp_version; echo esc_html( $wp_version ); ?></td>
				</tr>
				<tr>
					<th>PHP</th>
					<td><?php echo esc_html( phpversion() ); ?></td>
				</tr>
				<tr>
					<th>WooCommerce</th>
					<td><?php echo defined('WC_VERSION') ? esc_html(WC_VERSION) : '<span class="wc1c-badge wc1c-badge--error">Не активен</span>'; ?></td>
				</tr>
				<tr>
					<th>Поддержка ZipArchive</th>
					<td>
						<?php if ( class_exists('ZipArchive') ) : ?>
							<span class="wc1c-badge wc1c-badge--success">Да</span>
						<?php else : ?>
							<span class="wc1c-badge wc1c-badge--error">Нет</span>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>Поддержка SimpleXML</th>
					<td>
						<?php if ( function_exists('simplexml_load_file') ) : ?>
							<span class="wc1c-badge wc1c-badge--success">Да</span>
						<?php else : ?>
							<span class="wc1c-badge wc1c-badge--error">Нет</span>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>max_execution_time</th>
					<td><?php echo esc_html( ini_get('max_execution_time') ); ?> сек</td>
				</tr>
				<tr>
					<th>memory_limit</th>
					<td><?php echo esc_html( ini_get('memory_limit') ); ?></td>
				</tr>
				<tr>
					<th>upload_max_filesize</th>
					<td><?php echo esc_html( ini_get('upload_max_filesize') ); ?></td>
				</tr>
				<tr>
					<th>post_max_size</th>
					<td><?php echo esc_html( ini_get('post_max_size') ); ?></td>
				</tr>
				<tr>
					<th>Папка для файлов обмена</th>
					<td>
						<?php
						$upload_dir   = wp_upload_dir();
						$exchange_dir = $upload_dir['basedir'] . '/wc1c/';
						$writable     = wp_is_writable( $upload_dir['basedir'] );
						echo esc_html( $exchange_dir );
						echo $writable
							? ' <span class="wc1c-badge wc1c-badge--success">Доступна для записи</span>'
							: ' <span class="wc1c-badge wc1c-badge--error">Нет прав на запись</span>';
						?>
					</td>
				</tr>
				<tr>
					<th>Обмен включён</th>
					<td>
						<?php if ( get_option('wc1c_enable_exchange','0') === '1' ) : ?>
							<span class="wc1c-badge wc1c-badge--success">Да</span>
						<?php else : ?>
							<span class="wc1c-badge wc1c-badge--error">Нет</span>
							<a href="<?php echo esc_url( admin_url('admin.php?page=wc1c-settings') ); ?>" class="button button-secondary">Включить</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>URL обмена</th>
					<td>
						<code><?php echo esc_html( home_url('/?wc1c_exchange=1') ); ?></code>
						<button class="button button-secondary wc1c-copy-btn" data-copy="<?php echo esc_attr(home_url('/?wc1c_exchange=1')); ?>">Скопировать</button>
					</td>
				</tr>
			</table>
		</div>

	</div>

	<div class="wc1c-footer">
		<p>Разработчик: <a href="https://рукодер.рф/" target="_blank">Сергей Солошенко (РуКодер)</a> | 
		<a href="mailto:support@рукодер.рф">support@рукодер.рф</a> | 
		<a href="https://t.me/RussCoder" target="_blank">Telegram: @RussCoder</a></p>
	</div>
</div>
