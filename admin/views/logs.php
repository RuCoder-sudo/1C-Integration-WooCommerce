<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap wc1c-wrap">
	<div class="wc1c-header">
		<div class="wc1c-header__logo">
			<span class="dashicons dashicons-networking"></span>
		</div>
		<div class="wc1c-header__info">
			<h1>Логи обмена</h1>
			<p class="wc1c-header__subtitle">История событий синхронизации с 1С</p>
		</div>
	</div>

	<?php if ( $notice ) : ?>
		<div class="wc1c-notice wc1c-notice--<?php echo esc_attr( $notice['type'] ); ?>">
			<?php echo wp_kses_post( $notice['message'] ); ?>
		</div>
	<?php endif; ?>

	<div class="wc1c-logs-toolbar">
		<form method="get" style="display:inline-flex;gap:8px;align-items:center;">
			<input type="hidden" name="page" value="wc1c-logs">
			<select name="log_level" class="wc1c-select">
				<option value="">Все уровни</option>
				<option value="info" <?php selected( $level, 'info' ); ?>>Информация</option>
				<option value="success" <?php selected( $level, 'success' ); ?>>Успех</option>
				<option value="warning" <?php selected( $level, 'warning' ); ?>>Предупреждение</option>
				<option value="error" <?php selected( $level, 'error' ); ?>>Ошибка</option>
			</select>
			<button type="submit" class="button">Фильтр</button>
		</form>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline">
			<?php wp_nonce_field( 'wc1c_clear_logs' ); ?>
			<input type="hidden" name="action" value="wc1c_clear_logs">
			<button type="submit" class="button button-secondary" onclick="return confirm('Удалить все записи лога?')">
				<span class="dashicons dashicons-trash" style="margin-top:3px"></span> Очистить логи
			</button>
		</form>
	</div>

	<div class="wc1c-card" style="padding:0">
		<?php if ( ! empty( $data['logs'] ) ) : ?>
			<table class="wc1c-log-table widefat striped">
				<thead>
					<tr>
						<th style="width:160px">Время</th>
						<th style="width:100px">Уровень</th>
						<th>Сообщение</th>
						<th style="width:200px">Контекст</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $data['logs'] as $log ) : ?>
						<tr class="wc1c-log-row wc1c-log-row--<?php echo esc_attr( $log->log_level ); ?>">
							<td><?php echo esc_html( $log->log_time ); ?></td>
							<td>
								<span class="wc1c-badge wc1c-badge--<?php echo esc_attr( $log->log_level === 'success' ? 'success' : ( $log->log_level === 'error' ? 'error' : ( $log->log_level === 'warning' ? 'warning' : 'info' ) ) ); ?>">
									<?php
									$level_labels = array(
										'info'    => 'Инфо',
										'success' => 'Успех',
										'warning' => 'Внимание',
										'error'   => 'Ошибка',
									);
									echo esc_html( $level_labels[ $log->log_level ] ?? $log->log_level );
									?>
								</span>
							</td>
							<td><?php echo esc_html( $log->log_message ); ?></td>
							<td>
								<?php if ( $log->log_context ) : ?>
									<details>
										<summary>Подробности</summary>
										<pre><?php echo esc_html( json_encode( json_decode( $log->log_context ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) ); ?></pre>
									</details>
								<?php else : ?>
									—
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php
			$total_pages = ceil( $data['total'] / 50 );
			if ( $total_pages > 1 ) :
			?>
				<div class="wc1c-pagination">
					<?php for ( $i = 1; $i <= $total_pages; $i++ ) : ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc1c-logs&paged=' . $i . ( $level ? '&log_level=' . esc_attr($level) : '' ) ) ); ?>"
							class="button <?php echo $page === $i ? 'button-primary' : 'button-secondary'; ?>">
							<?php echo $i; ?>
						</a>
					<?php endfor; ?>
					<span class="wc1c-log-count">Всего записей: <?php echo esc_html( $data['total'] ); ?></span>
				</div>
			<?php endif; ?>

		<?php else : ?>
			<div class="wc1c-empty-state">
				<span class="dashicons dashicons-list-view"></span>
				<p>Записей лога нет<?php echo $level ? ' для уровня «' . esc_html($level) . '»' : ''; ?>.</p>
			</div>
		<?php endif; ?>
	</div>

	<div class="wc1c-footer">
		<p>Разработчик: <a href="https://рукодер.рф/" target="_blank">Сергей Солошенко (РуКодер)</a> | 
		<a href="mailto:support@рукодер.рф">support@рукодер.рф</a> | 
		<a href="https://t.me/RussCoder" target="_blank">Telegram: @RussCoder</a></p>
	</div>
</div>
