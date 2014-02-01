<?php
/**
 * Plugin Name: iMega Teleport
 * Plugin URI: http://teleport.imega.ru
 * Description: EN:Import your products from your 1C to your new WooCommerce store. RU:Обеспечивает взаимосвязь интернет-магазина и 1С.
 * Description: Ссылка для обмена
 * Version: 1.0
 * Author: iMega ltd
 * Author URI: http://imega.ru
 * Requires at least: 3.5
 * Tested up to: 3.5
 */
/**
 * Copyright 2013 iMega ltd (email: info@imega.ru)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
if (! defined ( 'ABSPATH' ))
	exit ();

if (! function_exists ( 'wp_authenticate' )) {
	include (ABSPATH . 'wp-includes/pluggable.php');
}

if (! class_exists ( 'iMegaTeleport' )) {
	define ( 'CONTAINS_ONLY_THE_CHANGES', 'СодержитТолькоИзменения' );
	define ( 'PACKAGEOFFERS', 'ПакетПредложений' );
	define ( 'PRICETYPES', 'ТипыЦен' );
	define ( 'PRICETYPE', 'ТипЦены' );
	define ( 'CURRENCY', 'Валюта' );
	define ( 'OFFERS', 'Предложения' );
	define ( 'OFFER', 'Предложение' );
	define ( 'BARCODE', 'Штрихкод' );
	define ( 'BASEUNIT', 'БазоваяЕдиница' );
	define ( 'PRODUCTFUTURES', 'ХарактеристикиТовара' );
	define ( 'PRODUCTFUTURE', 'ХарактеристикаТовара' );
	define ( 'PRICES', 'Цены' );
	define ( 'PRICE', 'Цена' );
	define ( 'REPRESENTATION', 'Представление' );
	define ( 'PRICETYPEID', 'ИдТипаЦены' );
	define ( 'PRICEBYUNIT', 'ЦенаЗаЕдиницу' );
	define ( 'UNIT', 'Единица' );
	define ( 'RATIO', 'Коэффициент' );
	define ( 'AMOUNT', 'Количество' );
	
	define ( 'KEY', 'Код' );
	define ( 'FULLNAME', 'НаименованиеПолное' );
	define ( 'INTERNATIONALABBREVIATION', 'МеждународноеСокращение' );
	define ( 'ARTICLE', 'Артикул' );
	
	define ( 'CLASSI', 'Классификатор' );
	define ( 'GROUPS', 'Группы' );
	define ( 'GROUP', 'Группа' );
	
	define ( 'CATALOG', 'Каталог' );
	define ( 'PRODUCTS', 'Товары' );
	define ( 'PRODUCT', 'Товар' );
	
	define ( 'ATTRIBUTEVALUES', 'ЗначенияРеквизитов' );
	define ( 'ATTRIBUTEVALUE', 'ЗначениеРеквизита' );
	define ( 'VALUE', 'Значение' );
	
	define ( 'ID', 'Ид' );
	define ( 'NAME', 'Наименование' );
	define ( 'DESC', 'Описание' );
	define ( 'IMAGE', 'Картинка' );
	
	define ( 'PROPERTIES', 'Свойства' );
	define ( 'PROPERY', 'Свойство' );
	define ( 'VALUETYPE', 'ТипЗначений' );
	define ( 'ATTRIBUTESVARIANTS', 'ВариантыЗначений' );
	define ( 'FORPRODUCT', 'ДляТоваров' );
	define ( 'VALUEID', 'ИдЗначения' );
	define ( 'DIC', 'Справочник' );
	
	define ( 'PROPERTYVALUES', 'ЗначенияСвойств' );
	define ( 'PROPERTYVALUE', 'ЗначенияСвойства' );
	
	/**
	 * iMegaTeleport Class
	 *
	 * @package iMegaExchanger
	 * @version 0.1
	 * @author iMega
	 */
	class iMegaTeleport {
		protected $error = false;
		protected $filenameActivate = 'activate.sql';
		protected $filenameClear = 'clear.sql';
		protected $filenameDeactivate = 'deactivate.sql';
		protected $filenameFullname = 'fullname.sql';
		protected $filenameImport = 'import.xml';
		protected $filenameOffers = 'offers.xml';
		protected $filenameTables = 'tabs.sql';
		protected $filenameBL = 'woocommerce.sql';
		protected $force = false;
		protected $gogo = false;
		protected $name = 'iMega Teleport';
		protected $maxAllowedPacket = 0;
		protected $mnemo = 'imegateleport';
		protected $mysqli = null;
		protected $query;
		protected $table_prefix = null;
		protected $upload_dir;
		protected $sets = array ();
		protected $zip = false;
		/**
		 * Конструктор класса
		 */
		public function __construct() {
			set_error_handler ( array (
					$this,
					'errorHandler' 
			) );
			/*
			 * Опции
			 */
			$this->options ();
			/*
			 * Если AJAX, то нет смысла дальше продолжать
			 */
			$this->routers ( true );
			$this->hooks ();
			/*
			 * Проверка установленного woocommerce
			 */
			$this->existsBusinessLogic ( 'woocommerce' );
			/*
			 * Проверка соединения с БД
			 */
			$this->connectMysql ();
			/*
			 * Проверка возможности записи на диск
			 */
			$filename = $this->path ( 'basedir' ) . $this->path ( $this->mnemo );
			$this->folder ( $filename );
			/*
			 * Настройки
			 */
			$this->sets ['fullname'] = get_option ( 'imegateleport-settings-fullname' );
			$this->sets ['kod'] = get_option ( 'imegateleport-settings-kod' );
			$this->sets ['postinstall'] = get_option ( 'imegateleport-settings-postinstall' );
			if ($this->progress () === false) {
				$this->gogo = $this->routers ();
			}
			/*
			 * if ((isset ( $_GET ['page'] ) && $_GET ['page'] == 'imegateleport_settings') || $this->progress () !== false || $this->sets ['postinstall'] == 'true') $this->progressAjaxScript ();
			 */
			if ($this->gogo === true) {
				// $this->progressAjaxScript ();
				$this->runQuery ();
				$this->log ( '==QUERY==' );
				$this->log ( $this->query );
				$this->run ();
			}
			$this->log ( '==END==' );
			restore_error_handler ();
		}
		/**
		 * Авторизация
		 *
		 * @param string $user        	
		 * @param string $pass        	
		 * @return bool
		 */
		protected function auth($user, $pass) {
			$result = false;
			
			$user = wp_authenticate ( $user, $pass );
			
			$isError = is_wp_error ( $user );
			$isRoleA = user_can ( $user, 'administrator' );
			$isRoleM = user_can ( $user, 'shop_manager' );
			
			if (! $isError || ($isRoleA && $isRoleM))
				$result = true;
			
			return $result;
		}
		/**
		 * Авторизация
		 *
		 * @param string $user        	
		 * @param string $pass        	
		 * @return wp_user
		 */
		protected function authEx($user, $pass) {
			$creds = array (
					'user_login' => $user,
					'user_password' => $pass,
					'remember' => true 
			);
			$user = wp_signon ( $creds, false );
			
			if (is_wp_error ( $user ))
				echo $user->get_error_message ();
			
			return $user;
		}
		/**
		 * Авторизация через куки
		 */
		function authWithCookie() {
		}
		/**
		 * Название логики магазина
		 * - woocommerce
		 * - ecommerce
		 * - eshop
		 *
		 * @param string $name        	
		 * @return string
		 */
		private function businessLogic($name) {
			$file = $this->loadFile ( $name . '.sql' );
			return $file;
		}
		/**
		 * Соединение с БД
		 */
		function connectMysql() {
			if ($this->error && $this->force === false) {
				return;
			}
			$this->mysqli = new mysqli ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
			if ($this->mysqli->connect_errno) {
				$this->error = $this->mysqli->connect_error;
			}
		}
		/**
		 * Cоздание групп из xml
		 *
		 * @param string $query        	
		 * @param array $groups        	
		 * @param int $parent        	
		 * @return void
		 */
		function createGroups(&$query, $groups, $parent = '') {
			foreach ( $groups as $group ) {
				
				$id = ( string ) $group->{ID};
				$name = ( string ) $group->{NAME};
				$slug = $this->translit ( $name );
				$name = $this->escape_string ( $name );
				
				$query .= "('{$id}','{$parent}','{$name}','{$slug}'),\n";
				
				if ($group->{GROUPS}->{GROUP})
					$this->createGroups ( $query, $group->{GROUPS}->{GROUP}, $id );
			}
		}
		/**
		 * Настройки
		 *
		 * @param string $param        	
		 * @param mixed $value        	
		 * @return bool
		 */
		function settings($param, $value) {
			$result = false;
			switch ($param) {
				case 'fullname' :
					$result = true;
					break;
				case 'kod' :
					$result = true;
					break;
				case 'postinstall' :
					delete_option ( 'imegateleport-settings-' . $param );
					$result = false;
					break;
				case 'zip' :
					$result = true;
					break;
			}
			if ($result) {
				update_option ( 'imegateleport-settings-' . $param, $value );
				$this->log ( "==UPDATE OPTION (imegateleport-settings-$param) = $value" );
			}
			return $result;
		}
		/**
		 * Статистика использования
		 *
		 * @return string
		 */
		function stat() {
			$stat = '';
			$stat2 = '';
			$stat3 = '';
			
			$groups = get_option ( 'imegateleport_stat_groups' );
			if ($groups)
				$stat .= ' новых групп ' . $groups . ',';
			
			$groups_rep = get_option ( 'imegateleport_stat_groups_replace' );
			if ($groups_rep)
				$stat .= ' обновлено групп ' . $groups_rep . ',';
			
			$goods = get_option ( 'imegateleport_stat_goods' );
			if ($goods)
				$stat2 .= ' новых товаров ' . $goods . ',';
			
			$goods_rep = get_option ( 'imegateleport_stat_goods_replace' );
			if ($goods_rep)
				$stat2 .= ' обновлено товаров ' . $goods . ',';
			
			$time = get_option ( 'imegateleport_stat_date' );
			if ($time)
				$stat3 .= ' (' . date_i18n ( 'j F Y', $time ) . ')';
			
			if ($groups && $groups_rep && $goods && $goods_rep)
				$stat = $stat . '<br>' . $stat2 . '<br>' . $stat3;
			else
				$stat = $stat . $stat2 . $stat3;
			
			return __ ( 'Status:' ) . $stat;
		}
		/**
		 * Получение выгрузки от 1с
		 *
		 * @return bool
		 */
		function transfer() {
			$result = false;
			
			switch ($_GET ['mode']) {
				case 'checkauth' :
					$login = $_SERVER ['PHP_AUTH_USER'];
					$pass = $_SERVER ['PHP_AUTH_PW'];
					if (defined ( 'IMEGATELEPORT_AUTH_USER' ))
						$login = IMEGATELEPORT_AUTH_USER;
					if (defined ( 'IMEGATELEPORT_AUTH_PW' ))
						$pass = IMEGATELEPORT_AUTH_PW;
					$user = $this->auth ( $login, $pass );
					$this->log ( "==USER AUTH==" );
					$this->log ( $user );
					if ($user)
						echo "success\n";
					else
						echo "fall\n";
					exit ();
					break;
				
				case 'init' :
					$maxBodySize = get_option ( 'imegateleport-settings-max-body-size', 0 );
					if (defined ( 'IMEGATELEPORT_MAX_BODY_SIZE' ))
						if (IMEGATELEPORT_MAX_BODY_SIZE > 0) {
							$maxBodySize = IMEGATELEPORT_MAX_BODY_SIZE;
						}
						/*
					 * if (! function_exists('wp_validate_auth_cookie()')) require_once(ABSPATH.'wp-includes/pluggable.php'); $id = wp_validate_auth_cookie('','auth'); $this->log($id); $this->log(ini_get('post_max_size')); echo "zip=no\n"; echo "file_limit="; //post_max_size upload_max_filesize echo $this->inBytes(ini_get('upload_max_filesize')); echo "\n";
					 */
					if ($this->zip)
						$zip = 'yes';
					else
						$zip = 'no';
					echo "zip={$zip}\n";
					$this->log ( "==ZIP SUPPORT = {$this->zip}" );
					// post_max_size upload_max_filesize
					$bytes = $this->inBytes ( ini_get ( 'upload_max_filesize' ) );
					if ($maxBodySize > 0) {
						$bytes = $maxBodySize;
					}
					$this->log ( "==MAX BODY SIZE = $bytes" );
					echo "file_limit=$bytes\n";
					exit ();
					break;
				
				case "file" :
					$post = file_get_contents ( 'php://input' ); // or $HTTP_RAW_POST_DATA
					$filename = $this->path ( 'basedir' ) . $this->path ( $this->mnemo ) . $_GET ['filename'];
					$this->folder ( dirname ( $filename ) );
					$mode = 'w';
					if ($filename == $this->temp ()) {
						$mode = 'a';
					}
					$f = fopen ( $filename, $mode );
					fwrite ( $f, $post );
					fclose ( $f );
					$this->temp ( $filename );
					echo "success\n";
					exit ();
					break;
				
				case "import" :
					$this->progress ( 14 );
					$filename = $this->temp ();
					if (! empty ( $filename ) && $this->zip === true) {
						if ($this->unzip ( $filename, dirname ( $filename ) . '/' ))
							unlink ( $filename );
					}
					$result = true;
					echo "success\n";
					$this->temp ( '', true );
					break;
			}
			return $result;
		}
		/**
		 * Конвертиррования в транслит
		 *
		 * @param string $str        	
		 * @param string $delimiter        	
		 * @return string
		 */
		function translit($str, $delimiter = '-') {
			$str = mb_strtolower ( $str, 'UTF-8' );
			$rus = mb_split ( '-', 'а-б-в-г-д-е-и-й-к-л-м-н-о-п-р-с-т-у-ф-х-ц-ы-э-ж-з-ч-ш-щ-ю-я' );
			$tra = mb_split ( '-', 'a-b-v-g-d-e-i-y-k-l-m-n-o-p-r-s-t-u-f-h-c-y-e-zh-z-ch-sh-shch-u-ya' );
			$str = str_replace ( $rus, $tra, $str );
			$str = preg_replace ( "/[^a-zA-Z0-9\/_|+ -]/", '', $str );
			$str = preg_replace ( "/[\/_|+ -]+/", $delimiter, $str );
			
			return $str;
		}
		/**
		 * Проверить наличие логики магазина
		 * - woocommerce
		 * - ecommerce
		 * - eshop
		 *
		 * @param string $name        	
		 * @return bool
		 */
		private function existsBusinessLogic($name) {
			if ($this->error && $this->force === false) {
				return;
			}
			$isInstall = false;
			$plugins = get_option ( 'active_plugins' );
			foreach ( $plugins as $key => $value ) {
				if (strpos ( $value, $name . '.php' ) !== false) {
					$isInstall = true;
					break;
				}
			}
			if (! $isInstall) {
				$message = __ ( 'Not yet' ) . ' ' . __ ( 'Plugin' ) . ' ' . $name . ' :(';
				$this->error = $message;
				return;
			}
			return $isInstall;
		}
		/**
		 * Создание рабочей директории
		 *
		 * @param string $filename        	
		 * @return void
		 */
		function folder($filename) {
			if (! wp_mkdir_p ( $filename )) {
				$this->error .= '. ' . $filename;
				return;
			}
			
			if (! is_writable ( $filename )) {
				$this->error = "The $filename is not writable.";
			}
		}
		/**
		 * Action Hooks
		 */
		function hooks() {
			/*
			 * Отобразить ссылки на проект
			 */
			add_filter ( 'plugin_action_links_' . plugin_basename ( __FILE__ ), array (
					$this,
					'pluginLinks' 
			) );
			/*
			 * Активация плагина
			 */
			register_activation_hook ( __FILE__, array (
					$this,
					'pluginActivation' 
			) );
			/*
			 * Деактивация плагина
			 */
			register_deactivation_hook ( __FILE__, array (
					$this,
					'pluginDeactivation' 
			) );
			/*
			 * Отправить уведомление пользователю
			 */
			add_action ( 'admin_notices', array (
					$this,
					'notice' 
			) );
			/*
			 * Проверка прогресса
			 */
			add_action ( 'wp_ajax_imega_teleport', array (
					$this,
					'progress' 
			) );
			/*
			 * Пункт меню с настройками
			 */
			add_action ( 'admin_menu', array (
					$this,
					'pluginMenuSettings' 
			) );
		}
		/**
		 * Возвращает запись 2M как байты
		 *
		 * @param string $val        	
		 * @return Ambigous <number, string>
		 */
		function inBytes($val) {
			$val = trim ( $val );
			$last = strtolower ( $val [strlen ( $val ) - 1] );
			switch ($last) {
				case 'g' :
					$val *= 1024;
				case 'm' :
					$val *= 1024;
				case 'k' :
					$val *= 1024;
			}
			return $val;
		}
		/**
		 * Загружает файл с текущей директори плагина
		 *
		 * @param string $filename        	
		 * @return string
		 */
		function loadFile($filename) {
			if ($this->error && $this->force === false) {
				return;
			}
			$dir = dirname ( __FILE__ );
			$text = file_get_contents ( "{$dir}/{$filename}" );
			$text = str_replace ( '{$table_prefix}', $this->table_prefix, $text );
			return $text;
		}
		/**
		 * Обработка ошибок
		 */
		function errorHandler($errno, $errstr, $errfile, $errline) {
			$this->error = $errstr;
			$this->log ( "==ERROR==" );
			$this->log ( "No:$errno, $errstr, FILE: $errfile, LINE: $errline" );
		}
		/**
		 * mysqli escape_string function wrap
		 *
		 * @param string $string_to_escape        	
		 */
		function escape_string($string_to_escape) {
			return $this->mysqli->escape_string ( $string_to_escape );
		}
		/**
		 * Загружает в текстовую строку import.xml
		 *
		 * @return string
		 */
		function loadImport() {
			if ($this->error && $this->force === false) {
				return;
			}
			$file = $this->path ( 'baseurl' ) . $this->path ( $this->mnemo ) . $this->filenameImport;
			try {
				$import = new SimpleXMLElement ( $file, 0, true );
			} catch ( Exception $e ) {
				return;
			}
			$catalog = $import->{CATALOG};
			$catalog_id = ( string ) $catalog [0]->{ID};
			$contains_only_the_changes = $import->{CATALOG}->attributes ()->{CONTAINS_ONLY_THE_CHANGES};
			
			$query = '';
			
			if ($contains_only_the_changes == 'false') {
				$query = $this->loadFile ( $this->filenameClear );
			}
			
			if (is_object ( $import->{CLASSI}->{GROUPS}->{GROUP} )) {
				$query .= "INSERT INTO {$this->table_prefix}imega_groups (guid, parent, title, slug) VALUES\n";
				$this->createGroups ( $query, $import->{CLASSI}->{GROUPS}->{GROUP} );
				$query = substr ( $query, 0, - 2 ) . ";\n";
			}
			
			if (is_object ( $import->{CLASSI}->{PROPERTIES}->{PROPERY} )) {
				
				$query .= "INSERT INTO {$this->table_prefix}imega_prop (guid, title, slug, val_type, parent_guid) VALUES \n";
				
				foreach ( $import->{CLASSI}->{PROPERTIES}->{PROPERY} as $propery ) {
					$id = ( string ) $propery->{ID};
					$name = ( string ) $propery->{NAME};
					$valueType = ( string ) $propery->{VALUETYPE};
					if ($valueType == DIC)
						$valueType = 'select';
					else
						$valueType = 'text';
					
					$slug = $this->translit ( $name );
					$name = $this->escape_string ( $name );
					
					$query .= "('{$id}','{$name}','{$slug}','{$valueType}',NULL),\n";
					
					if ($propery->{ATTRIBUTESVARIANTS})
						foreach ( $propery->{ATTRIBUTESVARIANTS}->{DIC} as $cat ) {
							$cat_valueid = ( string ) $cat->{VALUEID};
							$cat_value = ( string ) $cat->{VALUE};
							$cat_value_slug = $this->translit ( $cat_value );
							$cat_value = $this->escape_string ( $cat_value );
							$query .= "('{$cat_valueid}','{$cat_value}','{$cat_value_slug}',NULL,'{$id}'),\n";
						}
				}
				$query = substr ( $query, 0, - 2 ) . ";\n";
			}
			
			if (is_object ( $catalog->{PRODUCTS}->{PRODUCT} )) {
				
				$query .= "INSERT INTO {$this->table_prefix}imega_prod (title, descr, guid, slug, catalog_guid, article, img, img_prop) VALUES \n";
				$query_misc = "INSERT INTO {$this->table_prefix}imega_misc (type, guid, label, val, labelSlug, countAttr, valSlug, _visible) VALUES \n";
				
				foreach ( $catalog->{PRODUCTS}->{PRODUCT} as $product ) {
					
					$id = ( string ) $product->{ID};
					$name = ( string ) $product->{NAME};
					$desc = $this->escape_string ( $product->{DESC} );
					$img = $this->escape_string ( $product->{IMAGE} );
					$img_prop = '';
					$filename_abs = $this->path ( $this->mnemo ) . $img;
					$filename = $this->path ( 'basedir' ) . $filename_abs;
					if (file_exists ( $filename ) && ! empty ( $img )) {
						$imgx = getimagesize ( $filename );
						$arr = array (
								"width" => $imgx [0],
								"height" => $imgx [1],
								"file" => $filename_abs 
						);
						$img_prop = $this->escape_string ( serialize ( $arr ) );
					}
					
					$slug = $this->translit ( $name );
					$name = $this->escape_string ( $name );
					$article = $this->escape_string ( $product->{ARTICLE} );
					
					$query .= "('{$name}','{$desc}','{$id}','{$slug}','{$catalog_id}','{$article}','{$img}','{$img_prop}'),\n";
					
					foreach ( $product->{GROUPS} as $group ) {
						$group_id = ( string ) $group->{ID};
						$query_misc .= "('group','{$id}','{$group_id}',NULL,NULL,NULL,NULL,0),\n";
					}
					
					if ($product->{PROPERTYVALUES})
						foreach ( $product->{PROPERTYVALUES}->{PROPERTYVALUE} as $property ) {
							$propery_id = ( string ) $property->{ID};
							$property_value = ( string ) $property->{VALUE};
							$property_value_slug = $this->translit ( $property_value );
							$property_value = $this->escape_string ( $property_value );
							if (! empty ( $property_value ))
								$query_misc .= "('prop','{$id}','{$propery_id}','{$property_value}',NULL,NULL,'{$property_value_slug}',0),\n";
						}
					
					$countAttr = count ( $product->{ATTRIBUTEVALUES}->{ATTRIBUTEVALUE} );
					foreach ( $product->{ATTRIBUTEVALUES}->{ATTRIBUTEVALUE} as $attr ) {
						$attr_name = ( string ) $attr->{NAME};
						$attr_name_slug = $this->translit ( $attr_name );
						$attr_name = $this->escape_string ( $attr_name );
						$attr_value = ( string ) $attr->{VALUE};
						$attr_valueSlug = $this->escape_string ( $this->translit ( $attr_value ) );
						$attr_value = $this->escape_string ( $attr_value );
						if (! empty ( $attr_value )) {
							$visible = 0;
							if ($this->sets ['kod'] == 'true' && $attr_name_slug == 'kod')
								$visible = 1;
							$query_misc .= "('attr','{$id}','{$attr_name}','{$attr_value}','{$attr_name_slug}',$countAttr,'{$attr_valueSlug}', $visible),\n";
						}
					}
				}
				
				$query = substr ( $query, 0, - 2 ) . ";\n";
				$query_misc = substr ( $query_misc, 0, - 2 ) . ";\n";
			}
			$this->progress ( 25 );
			return $query . $query_misc;
		}
		/**
		 * Загружает файл предложений
		 *
		 * @return string
		 */
		function loadOffers() {
			if ($this->error && $this->force === false) {
				return;
			}
			$file = $this->path ( 'baseurl' ) . $this->path ( $this->mnemo ) . $this->filenameOffers;
			
			try {
				$offers = new SimpleXMLElement ( $file, 0, true );
			} catch ( Exception $e ) {
				return;
			}
			
			$packageoffers = $offers->{PACKAGEOFFERS};
			$packageoffers_id = ( string ) $packageoffers [0]->{ID};
			$contains_only_the_changes = $offers->{PACKAGEOFFERS}->attributes ()->{CONTAINS_ONLY_THE_CHANGES};
			
			$query = '';
			$query1 = '';
			$query2 = '';
			$query3 = '';
			
			if (is_object ( $packageoffers->{OFFERS}->{OFFER} )) {
				$query1 .= "INSERT INTO {$this->table_prefix}imega_offers(guid, prod_guid, barcode, title, base_unit, base_unit_key, base_unit_title, base_unit_int, amount, postType) VALUES \n";
				$query2 .= "INSERT INTO {$this->table_prefix}imega_offers_features(offer_guid, prodGuid, variantGuid, title, val, titleSlug, valSlug) VALUES \n";
				$query3 .= "INSERT INTO {$this->table_prefix}imega_offers_prices(offer_guid, title, price, currency, unit, ratio, type_guid) VALUES \n";
				foreach ( $packageoffers->{OFFERS}->{OFFER} as $offer ) {
					
					$id = ( string ) $offer->{ID};
					$prod_guid = substr ( $id, 0, 36 );
					$barcode = ( string ) $offer->{BARCODE};
					$name = ( string ) $offer->{NAME};
					$base_unit = ( string ) $offer->{BASEUNIT};
					$base_unit_key = $offer->{BASEUNIT}->attributes ()->{KEY};
					$base_unit_title = $offer->{BASEUNIT}->attributes ()->{FULLNAME};
					$base_unit_int = $offer->{BASEUNIT}->attributes ()->{INTERNATIONALABBREVIATION};
					$amount = ( float ) $offer->{AMOUNT};
					$postType = 'product_variation';
					
					$name = $this->escape_string ( $name );
					$base_unit = $this->escape_string ( $base_unit );
					$base_unit_title = $this->escape_string ( $base_unit_title );
					
					if ($offer->{PRODUCTFUTURES}) {
						foreach ( $offer->{PRODUCTFUTURES}->{PRODUCTFUTURE} as $future ) {
							$future_title = ( string ) $future->{NAME};
							$future_value = ( string ) $future->{VALUE};
							
							$future_title_slug = $this->translit ( $future_title );
							$future_value_slug = $this->translit ( $future_value );
							
							$future_title = $this->escape_string ( $future_title );
							$future_value = $this->escape_string ( $future_value );
							$doubleGuid = explode ( '#', $id );
							$query2 .= "('{$id}', '{$doubleGuid[0]}','{$doubleGuid[1]}','{$future_title}','{$future_value}','{$future_title_slug}','{$future_value_slug}'),\n";
						}
					} else {
						$postType = '';
					}
					
					if ($offer->{PRICES})
						foreach ( $offer->{PRICES}->{PRICE} as $price ) {
							$price_pred = $this->escape_string ( $price->{REPRESENTATION} );
							$price_typeid = $this->escape_string ( $price->{PRICETYPEID} );
							$price_byunit = ( float ) $price->{PRICEBYUNIT};
							$price_cur = $this->escape_string ( $price->{CURRENCY} );
							$price_unit = $this->escape_string ( $price->{UNIT} );
							$price_ratio = $this->escape_string ( $price->{RATIO} );
							$query3 .= "('{$id}','{$price_pred}',{$price_byunit},'{$price_cur}','{$price_unit}','{$price_ratio}','{$price_typeid}'),\n";
						}
					
					$query1 .= "('{$id}','{$prod_guid}','{$barcode}','{$name}','{$base_unit}','{$base_unit_key}','{$base_unit_title}','{$base_unit_int}',$amount,'{$postType}'),\n";
				}
				
				$query1 = substr ( $query1, 0, - 2 ) . ";\n";
				$query2 = substr ( $query2, 0, - 2 ) . ";\n";
				$query3 = substr ( $query3, 0, - 2 ) . ";\n";
				
				$query = $query . $query1 . $query2 . $query3;
			}
			$this->progress ( 50 );
			return $query;
		}
		
		/**
		 * LOG
		 *
		 * @param array $value        	
		 */
		protected function log($value) {
			if (defined ( 'IMEGATELEPORT_LOG' ))
				if (IMEGATELEPORT_LOG === true) {
					$f = fopen ( $this->path ( 'basedir' ) . 'imegateleport.log', 'a' );
					fwrite ( $f, print_r ( $value, true ) . "\n" );
					fclose ( $f );
				}
		}
		/**
		 * Обработка уведомлений и реакции пользователя на них
		 *
		 * @return void
		 */
		public function notice() {
			$this->progressBarScripts ();
			$postinstall = get_option ( 'imegateleport-settings-postinstall' );
			if ($postinstall == 'true') {
				$this->pluginMessage ( 'updated', 'Скопируйте ссылку <a href=' . get_site_url () . '>' . get_site_url () . '</a> в форму обмена с сайтом 1С', false, true );
			}
			if ((isset ( $_GET ['page'] ) && $_GET ['page'] == 'imegateleport_settings') || $this->progress () !== false || $this->sets ['postinstall'] == 'true' || $this->gogo) {
				$progress = $this->progress ();
				$this->pluginMessage ( 'updated', 'Update goods' . '<input id=iMegaExistProgress type=hidden value=' . $progress . '><div style="clear:both"></div><div id=iMegaTeleportProgressBar></div>', true, false );
			}
			
			if ($this->error && $this->force === false) {
				$this->pluginMessage ( 'error', $this->error, true, false );
				delete_option ( 'imegateleport_progress' );
			}
		}
		/**
		 * Опции
		 *
		 * @return void
		 */
		function options() {
			global $table_prefix;
			$this->table_prefix = $table_prefix;
			$this->upload_dir = wp_upload_dir ();
			
			if (defined ( 'IMEGATELEPORT_FORCE' ))
				if (IMEGATELEPORT_FORCE === true) {
					$this->force = IMEGATELEPORT_FORCE;
				}
			
			$zip = get_option ( 'imegateleport-settings-zip' );
			if ($zip == 'true') {
				$this->zip = true;
			}
			if (defined ( 'IMEGATELEPORT_ZIP' ))
				if (IMEGATELEPORT_ZIP === true) {
					$this->zip = IMEGATELEPORT_ZIP;
				}
			if (class_exists ( 'ZipArchive' ) && $this->zip === true) {
				$this->zip = true;
			} else {
				$this->zip = false;
			}
			$this->log ( '==OPTIONS==' );
			$this->log ( '    FORCE = ' . $this->force );
			$this->log ( '    ZIP = ' . $this->zip );
		}
		/**
		 * Возвращает запрошенный путь
		 *
		 * @param string $value        	
		 * @param bool $slash        	
		 * @return string
		 */
		function path($value, $slash = true) {
			$path = null;
			switch ($value) {
				case 'imegateleport' :
					$path = $this->mnemo . '_uploads';
					break;
				case 'basedir' :
					$path = $this->upload_dir ['basedir'];
					break;
				case 'baseurl' :
					$path = $this->upload_dir ['baseurl'];
					break;
			}
			if ($slash) {
				$slash = '/';
			} else {
				$slash = '';
			}
			return $path . $slash;
		}
		/**
		 * Активация плагина
		 */
		function pluginActivation() {
			update_option ( 'imegateleport-settings-postinstall', 'true' );
			update_option ( 'imegateleport-settings-zip', 'true' );
			$this->query = $this->loadFile ( $this->filenameActivate );
			$this->run ();
		}
		/**
		 * Деактивация плагина
		 */
		public function pluginDeactivation() {
			$this->query = $this->loadFile ( $this->filenameClear );
			$this->query .= $this->loadFile ( $this->filenameDeactivate );
			$this->run ();
		}
		/**
		 * Ссылки в панели плагина
		 *
		 * @access public
		 * @param array $links        	
		 * @return void
		 */
		function pluginLinks($links) {
			$pluginLinks = array (
					'<a href=' . admin_url ( 'options-general.php?page=imegateleport_settings' ) . '>' . __ ( 'Settings' ) . '</a>' 
			);
			
			return array_merge ( $pluginLinks, $links );
		}
		/**
		 * Сообщение в админке wordpress
		 *
		 * @param string $status
		 *        	Может принимать значение updated | error
		 * @param string $message        	
		 * @param int $link
		 *        	для обработки уведомления о закрытии
		 */
		function pluginMessage($status, $message, $hideClose = false, $id = false) {
			if (! $id) {
				$id = ' id=iMegaProgress';
			} else {
				$id = ' id=iMegaInfo';
			}
			print "<div class=\"$status\"$id><div style=\"float:left;width:90px;height:70px;background:url(" . plugins_url ( '/teleport.png', __FILE__ ) . ") no-repeat center\"></div><p><b>$this->name</b>";
			if (! $hideClose) {
				print " | <a id=imegaTeleportClose>" . __ ( 'Close' ) . "</a>";
			}
			print "<br>$message</p><div style=\"clear:both\"></div></div>";
		}
		/**
		 * Пунтк меню с настройками
		 */
		function pluginMenuSettings() {
			add_options_page ( __ ( 'Settings' ) . ' ' . $this->name, $this->name, 'manage_options', $this->mnemo . '_settings', array (
					$this,
					'pluginPageSettings' 
			) );
		}
		/**
		 * Страница с настройками
		 */
		function pluginPageSettings() {
			$text = $this->loadFile ( 'settings-form.htm' );
			$text = str_replace ( '{$title}', __ ( 'Settings' ) . ' ' . $this->name, $text );
			$text = str_replace ( '{$logo}', plugins_url ( '/teleport.png', __FILE__ ), $text );
			$text = str_replace ( '{$path}', $this->path ( 'basedir' ) . $this->path ( $this->mnemo ), $text );
			$text = str_replace ( '{$url}', get_site_url (), $text );
			$text = str_replace ( '{$stat}', $this->stat (), $text );
			$text = str_replace ( '{$feedback}', __ ( 'Feedback' ), $text );
			
			$checked = '';
			$name = get_option ( 'imegateleport-settings-fullname' );
			if ($name == 'true')
				$checked = ' checked value=1';
			$text = str_replace ( '{$checked_name}', $checked, $text );
			
			$checked = '';
			$name = get_option ( 'imegateleport-settings-kod' );
			if ($name == 'true')
				$checked = ' checked value=1';
			$text = str_replace ( '{$checked_kod}', $checked, $text );
			
			$checked = '';
			$zip = get_option ( 'imegateleport-settings-zip' );
			if ($zip == 'true')
				$checked = ' checked value=1';
			$text = str_replace ( '{$checked_zip}', $checked, $text );
			
			echo $text;
		}
		/**
		 * Отобразить прогресс или изменить значение
		 *
		 * @param int $value        	
		 * @return int
		 */
		function progress($value = null) {
			if ($this->error && $this->force === false) {
				return;
			}
			if (! isset ( $value )) {
				$value = get_option ( 'imegateleport_progress' );
				$this->log ( '    PROGRESS = ' . $value );
				if ($value == 100) {
					delete_option ( 'imegateleport_progress' );
				}
			} else {
				update_option ( 'imegateleport_progress', $value );
			}
			return $value;
		}
		/**
		 * Отправить скрипт в админку
		 *
		 * @return string
		 */
		function progressAjaxScript() {
			/*
			 * if ($this->error && $this->force === false) { return; } add_action ( 'admin_init', array ( $this, 'progressBarScripts' ) ); add_action ( 'admin_menu', array ( $this, 'progressBarShow' ) );
			 */
		}
		function progressBarScripts() {
			wp_enqueue_style ( 'jquery-ui-style-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css?ver=3.8' );
			wp_enqueue_script ( 'jquery-ui-progressbar' );
			wp_register_script ( 'iMegaTeleportProgressBar', plugins_url ( '/imegateleport.js', __FILE__ ) );
			wp_enqueue_script ( 'iMegaTeleportProgressBar' );
		}
		function progressBarShow() {
			$progress = $this->progress ();
			$this->pluginMessage ( 'updated', 'Update goods' . '<input id=iMegaExistProgress type=hidden value=' . $progress . '><div style="clear:both"></div><div id=iMegaTeleportProgressBar></div>', true, false );
		}
		/**
		 * Маршруты
		 *
		 * @param bool $ajax        	
		 * @return bool
		 */
		function routers($ajax = false) {
			$this->log ( '==GET==' );
			$this->log ( $_GET );
			$this->log ( '==POST==' );
			$this->log ( $_POST );
			$this->log ( '==SERVER==' );
			$this->log ( $_SERVER );
			if ($this->error && $this->force === false) {
				return;
			}
			$result = false;
			/*
			 * Проверка прогресса
			 */
			if ($ajax && isset ( $_POST ['action'] )) {
				$this->log ( '==AJAX==' );
				switch ($_POST ['action']) {
					case 'imega_teleport' :
						$progress = $this->progress ();
						echo $progress;
						$this->log ( "==PROGRESS = $progress" );
						exit ();
						break;
					case 'imegateleport-settings' :
						echo $this->settings ( $_POST ['param'], $_POST ['value'] );
						exit ();
						break;
				}
			}
			/*
			 * Проверка обращения клиента 1с
			 */
			$agent = '';
			if (isset ($_SERVER ['HTTP_USER_AGENT']))
				$agent = strstr ( $_SERVER ['HTTP_USER_AGENT'], '/', true );
			if (! $ajax && $agent == '1C+Enterprise' && $_GET ['type'] == 'catalog') {
				return $this->transfer ();
			}
			/*
			 * Проверка оффлайнового обновления
			 */
			if (! $ajax && isset ( $_POST ['action'] )) {
				if ($_POST ['action'] == 'imegagogo') {
					$this->progress ( 15 );
					$result = true;
				}
			}
			return $result;
		}
		/**
		 * Запуск
		 */
		function run() {
			if ($this->error && $this->force === false) {
				return;
			}
			
			if (! $this->mysqli->multi_query ( 'SET NAMES ' . DB_CHARSET )) {
				$this->error = $this->mysqli->connect_error;
				return;
			}
			
			/*
			 * // TODO next version $select = $this->mysqli->query ( "SELECT @@global.max_allowed_packet" ); if (! $select) { $this->error = $this->mysqli->connect_error; return; } else { $size = $select->fetch_array(); $this->maxAllowedPacket = $size[0]; } if ($this->maxAllowedPacket > 0) { }
			 */
			
			if (! $this->mysqli->multi_query ( $this->query )) {
				$this->error = $this->mysqli->connect_error;
				return;
			}
			$this->mysqli->close ();
		}
		/**
		 * Сбор запроса
		 *
		 * @return string
		 */
		function runQuery() {
			if ($this->error && $this->force === false) {
				return;
			}
			/*
			 * 1. create tables
			 */
			$query = $this->loadFile ( $this->filenameTables );
			/*
			 * 2. load import xml
			 */
			$query .= $this->loadImport ();
			/*
			 * 2.5 Если полное наименование сделать основным
			 */
			if ($this->sets ['fullname'] == 'true')
				$query .= $this->loadFile ( $this->filenameFullname );
				/*
			 * 3. load offers xml
			 */
			$query .= $this->loadOffers ();
			/*
			 * 4. business logic
			 */
			$queryBL = $this->loadFile ( $this->filenameBL );
			$queryBL = str_replace ( '{$baseurl}', $this->path ( 'baseurl' ), $queryBL );
			$queryBL = str_replace ( '{$imgpath}', $this->path ( $this->mnemo ), $queryBL );
			$query .= $queryBL;
			$queryBL = '';
			$this->query = $query;
		}
		/**
		 * Временный файл
		 */
		function temp($value = '', $remove = false) {
			$contents = '';
			if (empty ( $value )) {
				$mode = 'r';
			} else {
				$mode = 'w';
			}
			
			$filename = $this->path ( 'basedir' ) . $this->path ( $this->mnemo ) . 'imegatemp';
			
			if ($remove) {
				unlink ( $filename );
				return;
			}
			
			$f = fopen ( $filename, $mode );
			
			if (empty ( $value )) {
				$contents = fread ( $f, filesize ( $filename ) );
			} else {
				fwrite ( $f, $value );
			}
			
			fclose ( $f );
			return $contents;
		}
		/**
		 * Unzip the zip-file in the destination dir
		 *
		 * @param string $zipFile        	
		 * @param string $destDir        	
		 */
		function unzip($zipFile, $destDir = false) {
			if (class_exists ( 'ZipArchive' )) {
				$zip = new ZipArchive ();
				if ($zip) {
					$open = $zip->open ( $zipFile );
					if ($open === true) {
						$zip->extractTo ( $destDir );
						$zip->close ();
					} else {
						$this->error = '==ZIP ERROR: ' . $this->zipStatusString ( $open );
					}
					return true;
				}
			} else {
				return false;
			}
		}
		/**
		 * There is a usefull function to get the ZipArchive status
		 * as a human readable string
		 *
		 * @param int $status        	
		 * @return string
		 * @author Bruno Vibert <bruno.vibert@bonobox.fr>
		 */
		function zipStatusString($status) {
			switch (( int ) $status) {
				case ZipArchive::ER_OK :
					return 'N No error';
				case ZipArchive::ER_MULTIDISK :
					return 'N Multi-disk zip archives not supported';
				case ZipArchive::ER_RENAME :
					return 'S Renaming temporary file failed';
				case ZipArchive::ER_CLOSE :
					return 'S Closing zip archive failed';
				case ZipArchive::ER_SEEK :
					return 'S Seek error';
				case ZipArchive::ER_READ :
					return 'S Read error';
				case ZipArchive::ER_WRITE :
					return 'S Write error';
				case ZipArchive::ER_CRC :
					return 'N CRC error';
				case ZipArchive::ER_ZIPCLOSED :
					return 'N Containing zip archive was closed';
				case ZipArchive::ER_NOENT :
					return 'N No such file';
				case ZipArchive::ER_EXISTS :
					return 'N File already exists';
				case ZipArchive::ER_OPEN :
					return 'S Can\'t open file';
				case ZipArchive::ER_TMPOPEN :
					return 'S Failure to create temporary file';
				case ZipArchive::ER_ZLIB :
					return 'Z Zlib error';
				case ZipArchive::ER_MEMORY :
					return 'N Malloc failure';
				case ZipArchive::ER_CHANGED :
					return 'N Entry has been changed';
				case ZipArchive::ER_COMPNOTSUPP :
					return 'N Compression method not supported';
				case ZipArchive::ER_EOF :
					return 'N Premature EOF';
				case ZipArchive::ER_INVAL :
					return 'N Invalid argument';
				case ZipArchive::ER_NOZIP :
					return 'N Not a zip archive';
				case ZipArchive::ER_INTERNAL :
					return 'N Internal error';
				case ZipArchive::ER_INCONS :
					return 'N Zip archive inconsistent';
				case ZipArchive::ER_REMOVE :
					return 'S Can\'t remove file';
				case ZipArchive::ER_DELETED :
					return 'N Entry has been deleted';
				
				default :
					return sprintf ( 'Unknown status %s', $status );
			}
		}
	}
	
	$GLOBALS ['iMegaTeleport'] = new iMegaTeleport ();
}