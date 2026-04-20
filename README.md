# 1C-Integration-WooCommerce
1C-Integration-WooCommerce - Интеграция WooCommerce 1С:Предприятие

<p align="center"> <img src="assets/banner.png" alt="1C-Integration-WooCommerce — синхронизация товаров, заказов и остатков"> </p>

1C-Integration-WooCommerce - это плагин для WordPress, который обеспечивает полноценный обмен данными между вашим интернет-магазином на WooCommerce и учётными системами: 1С:Предприятие 8, СБИС и МойСклад. Гибкие настройки позволяют адаптировать синхронизацию под любые бизнес-сценарии.

🚀 Основные возможности
📦 Синхронизация каталога товаров
Выгрузка групп товаров (категорий) с учётом вложенности.

Синхронизация товаров и вариаций (характеристик).

Импорт свойств товаров и их значений.

Загрузка изображений товаров и изображений для вариаций.

Синхронизация габаритов и веса товаров.

💰 Цены и остатки
Загрузка нескольких типов цен из учётной системы.

Поддержка базовой цены и цены распродажи.

Синхронизация остатков товаров на складах.

Возможность выбора складов для учёта остатков.

📋 Обмен заказами
Выгрузка заказов с сайта в учётную систему (1С, СБИС, МойСклад).

Поддержка real-time обмена заказами.

Изменение статуса заказов на сайте по данным из учётной системы.

Применение изменений в составе заказа по данным из учётной системы.

🛠️ Гибкие настройки и технологии
Простая настройка: Для базовой работы достаточно указать логин и пароль. У каждой настройки есть всплывающая подсказка.

Работа на любом хостинге: Пошаговая обработка данных и контроль времени выполнения скрипта позволяют использовать плагин даже на дешёвом shared-хостинге.

Оптимизация ресурсов: Обновление товара происходит только при реальных изменениях, используется контроль по хешу содержимого.

Работа с архивами: Поддержка сжатых zip-архивов для ускорения передачи данных, особенно при большом количестве изображений.

Управление частями файлов: Настройка размера части для обхода ограничений хостинга на максимальный размер передаваемых данных.

Интеллектуальный поиск: Поиск существующих товаров по артикулу или категорий по названию с вложенностью, чтобы избежать дублирования.

Логирование: Полная запись всех событий для отладки и мониторинга.

⚙️ Совместимость
Компонент	Поддержка
WordPress	5.8 – 6.7
PHP	7.4+
WooCommerce	4.0+
1С:Предприятие 8	✔️ (любые конфигурации с CML2/CML3)
СБИС	✔️
МойСклад	✔️
📥 Установка
Скачайте архив плагина или клонируйте репозиторий:

bash
git clone https://github.com/RuCoder-sudo/1C-Integration-WooCommerce.git
Загрузите папку 1C-Integration-WooCommerce в /wp-content/plugins/.

Активируйте плагин через меню «Плагины» в WordPress.

Перейдите в раздел WooCommerce → 1C Обмен данными.

Заполните базовые настройки:

Придумайте и введите Пользователь и Пароль (будут использоваться для авторизации узла обмена в 1С).

Отметьте чекбокс «Включить обмен».

Сохраните настройки. Адрес, пользователь и пароль теперь можно использовать при настройке узла обмена в вашей учётной системе.

💡 Совет: Перед настройкой на боевом сайте протестируйте процесс синхронизации на тестовом сайте.

❓ Часто задаваемые вопросы
Где взять адрес для настройки узла обмена в 1С?
После включения плагина и сохранения настроек в разделе WooCommerce → 1C Обмен данными, используйте URL вашего сайта. Более подробная информация об адресах будет доступна в интерфейсе плагина.

Как узнать, какие типы цен выгружаются?
Типы цен автоматически заполнятся при первом успешном обмене. После этого вы сможете выбрать, какой тип цен использовать на сайте как базовый.

Работает ли плагин на дешёвом хостинге?
Да. Плагин использует пошаговую обработку данных, что позволяет работать даже в условиях ограниченных лимитов shared-хостинга.

Можно ли отключить синхронизацию определённых данных?
Да. В настройках плагина можно гибко отключить запись или обновление отдельных типов данных (например, категорий, свойств, изображений).

Поддерживает ли плагин облачные конфигурации 1С?
Да, плагин работает с любой конфигурацией (облачной и нет), которая поддерживает стандарт обмена CML2/CML3.

🧪 Статус проекта
✅ Стабильная работа на WordPress 6.7
✅ Проверено с PHP 8.0 – 8.3
✅ Все функции протестированы
✅ Готов к использованию в продакшене

📌 Лицензия
GPL v2 or later
Полный текст лицензии: https://www.gnu.org/licenses/gpl-2.0.html

👨‍💻 Автор
Sergey Soloshenko (RuCoder)
🛠 WordPress / Full Stack разработчик
📬 support@рукодер.рф
📲 Telegram: @RussCoder
🌐 https://рукодер.рф

1C-Integration-WooCommerce — WooCommerce Integration with 1C, SBIS, and MoySklad
1C-Integration-WooCommerce is a WordPress plugin that provides a full data exchange between your WooCommerce store and accounting systems: 1C:Enterprise 8, SBIS, and MoySklad. Flexible settings allow you to adapt synchronization to any business scenario.

🚀 Key Features
📦 Product Catalog Synchronization
Export of product groups (categories) with nesting.

Synchronization of products and variations (characteristics).

Import of product properties and their values.

Upload of product images and images for variations.

Synchronization of product dimensions and weight.

💰 Prices and Stock Balances
Upload of multiple price types from the accounting system.

Support for regular price and sale price.

Synchronization of stock balances across warehouses.

Ability to select which warehouses to consider for stock.

📋 Order Exchange
Upload of orders from the site to the accounting system (1C, SBIS, MoySklad).

Real-time order exchange support.

Order status change on the site based on data from the accounting system.

Application of changes to the order composition based on data from the accounting system.

🛠️ Flexible Settings & Technologies
Simple Setup: Basic operation requires only a login and password. Each setting has a tooltip.

Works on Any Hosting: Step-by-step data processing and script execution time control allow the plugin to work even on cheap shared hosting.

Resource Optimization: Product updates occur only when there are real changes; content hash control is used.

Archive Support: Support for compressed zip archives to speed up data transfer, especially with many images.

File Part Management: Configurable file part size to bypass hosting limits on maximum data transfer size.

Smart Search: Search for existing products by SKU or categories by name with nesting to avoid duplication.

Logging: Full logging of all events for debugging and monitoring.

⚙️ Compatibility
Component	Support
WordPress	5.8 – 6.7
PHP	7.4+
WooCommerce	4.0+
1C:Enterprise 8	✔️ (any configuration with CML2/CML3)
SBIS	✔️
MoySklad	✔️
📥 Installation
Download the plugin archive or clone the repository:

bash
git clone https://github.com/RuCoder-sudo/1C-Integration-WooCommerce.git
Upload the 1C-Integration-WooCommerce folder to /wp-content/plugins/.

Activate the plugin via the "Plugins" menu in WordPress.

Go to WooCommerce → 1C Data Exchange.

Configure the basic settings:

Create and enter a Username and Password (will be used for exchange node authorization in 1C).

Check the "Enable Exchange" checkbox.

Save the settings. The URL, username, and password can now be used when configuring the exchange node in your accounting system.

💡 Tip: Before configuring on your production site, test the synchronization process on a staging site.

❓ FAQ
Where can I get the URL for setting up the exchange node in 1C?
After enabling the plugin and saving the settings in the WooCommerce → 1C Data Exchange section, use your site's URL. More detailed information about the URLs will be available in the plugin interface.

How do I know which price types are being uploaded?
Price types will be automatically populated during the first successful exchange. After that, you can select which price type to use as the base price on the site.

Does the plugin work on cheap hosting?
Yes. The plugin uses step-by-step data processing, allowing it to work even under the limited resources of shared hosting.

Can I disable the synchronization of certain data?
Yes. The plugin settings allow you to flexibly disable the recording or updating of specific data types (e.g., categories, properties, images).

Does the plugin support cloud-based 1C configurations?
Yes, the plugin works with any configuration (cloud-based or on-premise) that supports the CML2/CML3 exchange standard.

🧪 Project Status
✅ Stable with WordPress 6.7
✅ Tested with PHP 8.0 – 8.3
✅ All features tested
✅ Production-ready

📌 License
GPL v2 or later
Full license: https://www.gnu.org/licenses/gpl-2.0.html

👨‍💻 Author
Sergey Soloshenko (RuCoder)
🛠 WordPress / Full Stack Developer
📬 support@рукодер.рф
📲 Telegram: @RussCoder
🌐 https://рукодер.рф
