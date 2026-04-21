<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap wc1c-wrap">
	<div class="wc1c-header">
		<div class="wc1c-header__logo">
			<span class="dashicons dashicons-networking"></span>
		</div>
		<div class="wc1c-header__info">
			<h1>Инструкции по настройке</h1>
			<p class="wc1c-header__subtitle">Пошаговое руководство по интеграции с 1С, СБИС и МойСклад</p>
		</div>
	</div>

	<div class="wc1c-help-grid">

		<div class="wc1c-card">
			<h2><span class="dashicons dashicons-admin-plugins"></span> Шаг 1: Установка и активация</h2>
			<ol class="wc1c-steps">
				<li>Установите и активируйте плагин через «Плагины → Добавить новый → Загрузить плагин».</li>
				<li>Перейдите в меню <strong>1C Integration → Активация</strong>.</li>
				<li>Введите лицензионный ключ в формате <code>WC1C-XXXX-XXXX-XXXX-XXXX</code> и нажмите «Активировать».</li>
				<li>Если у вас нет ключа — заполните форму запроса. Разработчик пришлёт ключ на email.</li>
			</ol>
		</div>

		<div class="wc1c-card">
			<h2><span class="dashicons dashicons-admin-settings"></span> Шаг 2: Настройки обмена на сайте</h2>
			<ol class="wc1c-steps">
				<li>Перейдите в <strong>1C Integration → Настройки</strong>.</li>
				<li>Придумайте и заполните поле <strong>«Пользователь»</strong>.</li>
				<li>Придумайте и заполните поле <strong>«Пароль»</strong>.</li>
				<li>Включите чекбокс <strong>«Включить обмен»</strong>.</li>
				<li>Нажмите <strong>«Сохранить настройки»</strong>.</li>
				<li>Скопируйте <strong>URL обмена</strong> — он понадобится в следующем шаге.</li>
			</ol>
			<div class="wc1c-info-box">
				<strong>URL обмена:</strong> <code><?php echo esc_html( home_url('/?wc1c_exchange=1') ); ?></code>
			</div>
		</div>

		<div class="wc1c-card">
			<h2><span class="dashicons dashicons-database-export"></span> Шаг 3: Настройка в 1С (Управление торговлей, УТ)</h2>
			<ol class="wc1c-steps">
				<li>Откройте 1С и перейдите: <strong>НСИ и администрирование → Интеграция с другими программами → Обмен с сайтом</strong>.</li>
				<li>Создайте новый узел обмена.</li>
				<li>В поле <strong>«Адрес публикации на веб-сервере»</strong> введите URL обмена с сайта.</li>
				<li>Заполните поля <strong>«Пользователь»</strong> и <strong>«Пароль»</strong> — те же, что задали в настройках плагина.</li>
				<li>Выберите нужные параметры выгрузки (полный обмен / только изменения, выгрузка заказов и т.д.).</li>
				<li>Запустите тестовый обмен и убедитесь, что он прошёл без ошибок.</li>
			</ol>
		</div>

		<div class="wc1c-card">
			<h2><span class="dashicons dashicons-cloud-upload"></span> СБИС: Настройка обмена</h2>
			<ol class="wc1c-steps">
				<li>В СБИС перейдите: <strong>Розница → Магазины → Настройки обмена с сайтом</strong>.</li>
				<li>Укажите URL обмена, пользователя и пароль из настроек плагина.</li>
				<li>Настройте расписание синхронизации.</li>
			</ol>
			<p><a href="https://sbis.ru/help/roz/stores/comm" target="_blank" class="button button-secondary">Документация СБИС →</a></p>
		</div>

		<div class="wc1c-card">
			<h2><span class="dashicons dashicons-store"></span> МойСклад: Настройка обмена</h2>
			<ol class="wc1c-steps">
				<li>В МойСклад перейдите: <strong>Настройки → Интеграция → WooCommerce</strong>.</li>
				<li>Укажите URL вашего сайта, логин и пароль из настроек плагина.</li>
				<li>Настройте маппинг полей и запустите синхронизацию.</li>
			</ol>
			<p><a href="https://support.moysklad.ru/hc/ru/articles/4416274519953" target="_blank" class="button button-secondary">Документация МойСклад →</a></p>
		</div>

		<div class="wc1c-card">
			<h2><span class="dashicons dashicons-warning"></span> Возможные проблемы</h2>
			<dl class="wc1c-faq">
				<dt>1С пишет «Ошибка авторизации»</dt>
				<dd>Проверьте, что пользователь и пароль в настройках плагина совпадают с теми, что вы вводите в 1С. URL обмена должен быть без конечного слэша.</dd>

				<dt>Товары не создаются</dt>
				<dd>Убедитесь, что включён импорт (чекбоксы в Настройках). Проверьте вкладку «Логи» — там будут подробности ошибки.</dd>

				<dt>Обмен зависает</dt>
				<dd>Увеличьте «Лимит времени» и «Размер части файла» в настройках. Включите ZIP-архивацию для ускорения передачи.</dd>

				<dt>Цены и остатки не обновляются</dt>
				<dd>Убедитесь, что в настройках включено «Обновлять цены» и «Обновлять остатки». При первом обмене тип цен записывается автоматически.</dd>

				<dt>Ошибка 403 при обмене</dt>
				<dd>Убедитесь, что обмен включён в настройках (чекбокс «Включить обмен»). Проверьте, нет ли кэширующих плагинов, которые могут блокировать запросы.</dd>
			</dl>
		</div>

		<div class="wc1c-card">
			<h2><span class="dashicons dashicons-phone"></span> Поддержка и контакты</h2>
			<div class="wc1c-contacts">
				<div class="wc1c-contact-item">
					<span class="dashicons dashicons-admin-site"></span>
					<a href="https://рукодер.рф/" target="_blank">рукодер.рф</a>
				</div>
				<div class="wc1c-contact-item">
					<span class="dashicons dashicons-email"></span>
					<a href="mailto:support@рукодер.рф">support@рукодер.рф</a>
				</div>
				<div class="wc1c-contact-item">
					<span class="dashicons dashicons-format-chat"></span>
					<a href="https://t.me/RussCoder" target="_blank">Telegram: @RussCoder</a>
				</div>
				<div class="wc1c-contact-item">
					<span class="dashicons dashicons-phone"></span>
					<span>+7 (985) 985-53-97 (WhatsApp)</span>
				</div>
				<div class="wc1c-contact-item">
					<span class="dashicons dashicons-nametag"></span>
					<span>GitHub: <a href="https://github.com/RuCoder-sudo" target="_blank">RuCoder-sudo</a></span>
				</div>
			</div>
			<p class="description" style="margin-top:16px">
				Разработчик: Сергей Солошенко | РуКодер<br>
				Специализация: Веб-разработка с 2018 года | WordPress / Full Stack<br>
				Принцип работы: «Сайт как для себя»
			</p>
		</div>

	</div>
</div>
