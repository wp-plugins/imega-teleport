=== iMega Teleport ===
Contributors: iMega
Donate link: http://teleport.imega.ru/donate
Tags: 1C, import, viper, , ecommerce, e-commerce, commerce, woothemes, wordpress ecommerce, affiliate, store, sales, sell, shop, shopping, cart, checkout, configurable, variable, widgets, reports, download, downloadable, digital, inventory, stock, reports, shipping, tax
Requires at least: 3.5
Tested up to: 4.0.1
Stable tag: 4.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Import your products from your 1C to your eShop.
Взаимосвязь интернет-магазина и 1С.

== Description ==

= In English. =

Import your products from your 1C to your eShop.
Import data that contain title of goods, price, properties 
and characteristics of the goods, description and picture, 
the amount of goods available for sale.
Export orders and change status of orders.

= На русском. =

iMegaTeleport обеспечивает взаимосвязь интернет-магазина и 1С 
через базовый модуль «обмен 1С и сайта», встроенного 
в конфигурациях 1С: Управление торговлей, Торговля и склад, 
Управление производственным предприятием, а также некоторых 
других продуктах 1С.

iMegaTeleport выгружает данные о товаре: название, цена, 
свойства и характеристики товара, описание и изображение, 
доступный остаток товара, а также структуру каталога 
товаров (группы номенклатуры).

Количество товаров, которое можно выгрузить ограничено 
возможностями сервера, на котором работает программа 1С.

Обрабатывает заказы покупателей, используя статусы: 
"В обработке", "Завершен", "Отменен".

Совместимо с:
	тема Viper by Fabthemes,
	плагин WooCommerce.

Ознакомьтесь пожалуйста с [инструкцией](http://teleport.imega.ru/instructions).

== Installation ==

= In English. =

1. Download the plugin file.
2. Unzip the file into a folder on your hard drive.
3. Upload the /imegateleport/ folder to the /wp-content/plugins/ folder on your site.
4. Visit Plugins menu and Activate the plugin.
4. Go to the plugin settings page.

= На русском. =

1. Загрузите iMegaTeleport и распакуйте его.
2. Перенесите директорию iMegaTeleport в wp-content/plugins/ блога.
3. Зайдите на страницу «Плагины» в панели управления блогом 
   и нажмите «Активировать» у iMegaTeleport.
4. Выберите пункт iMegaTeleport в меню Параметры, для настройки.
5. [Настройка 1С для работы с интернет-магазином](http://teleport.imega.ru/instructions#ch3)
6. [Создание в 1С узла обмена с интернет-магазином](http://teleport.imega.ru/instructions#ch4)

== Frequently Asked Questions ==

= What do if the progress bar is stopped? =

1. Visit Plugins menu Deactivate plugin and Activate it.
2. Try again import your products from your 1C with parameter "Complete import".

= Что делать если индикатор процесса завис? =

1. Деактивируйте iMegaTeleport в панели управления блогом и снова активируйте.
2. Выполните обмен с сайтом, но указав полную выгрузку товара. 

= Что делать если 1C не загружает заказы, ругается на валюту? =
1. Укажите валюту в 1С из классификатора ОКВ (наименование RUB, Код 643).
2. В настройках интернет-магазина, также укажите RUB.

= ErrorNo:2, mysqli::mysqli() [mysqli.mysqli]: (HY000/2002): Connection refused =
1. Укажите iMegaTeleport IP-адрес сервера MySQL define('IMEGATELEPORT_HOST', '255.255.255.255'); в wp-config.php

== Screenshots ==

1. Settings of a plugin.

== Changelog ==

= 1.6.14 =
New option Использовать файл описания товара
New option В поле SKU отображать Артикул (по умолчанию Штрихкод)
New option Остатки товаров на складах
Fix accepted files
Add WAREHOUSES
Add ADDRESS the orders
Fix queries and js

= 1.6.12 =
Fix queries and js

= 1.6.9 =
Fix shipping address of order.

= 1.6.8 =
Fix discount of order.

= 1.6.7 =
Fix convert property of products to translite.

= 1.6.6 =
Add to order shipping cost.

= 1.6.5 =
Fix a order
Fix composer
Fix queries

= 1.6.4 =
Fix properties of products without translate.
Fix value size taxonomy for term_taxonomy
Fix long ID of products
Fix fields of order set optional

= 1.6.3 =
Fix field 'operation' of order

= 1.6.2 =
Fix order.xml

= 1.6.1 =
Fix error scandir() expects parameter 2 to be long, string given
Fix error Undefined index: PHP_AUTH_PW

= 1.6 =
Fix parser xml
Change folder for order.xml

= 1.5 =
Added IMEGATELEPORT_HOST is a PHP constant. The host MySQL. Can be either a host name or an IP address. 
Added IMEGATELEPORT_PORT is a PHP constant. Specifies the port number to attempt to connect to the MySQL server.
Added IMEGATELEPORT_SOCKET is a PHP constant. Specifies the socket or named pipe that should be used.
Added IMEGATELEPORT_USER is a PHP constant. The user MySQL.
Added IMEGATELEPORT_PASSWORD is a PHP constant. The pass.
Fix display errors

= 1.4 =
Fix bug to dependen fields of table posts.post_title and post_context not null.
Added IMEGATELEPORT_IGNORE_ACCESS is a PHP constant. The script doesn't control access to mysql.﻿

= 1.3 =
Fix bug 50%.

= 1.2 =
Added support the Viper by Fabthemes. Viper is a free directory WordPress theme.

= 1.1 =
Added export orders and change status of orders.

= 1.0 =
Added IMEGATELEPORT_LOG is a PHP constant. It makes log.
Added IMEGATELEPORT_FORCE is a PHP constant. The script to force skip errors.
Added IMEGATELEPORT_MAX_BODY_SIZE is a PHP constant. The maximum size of an uploaded file.
Added IMEGATELEPORT_ZIP is a PHP constant. For use a compressed file.
Fix error message in header.

= 0.1 =
First official release!

== Upgrade Notice ==
has not yet been published.