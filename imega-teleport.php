<?php
/**
 * Plugin Name: iMega Teleport
 * Plugin URI: http://teleport.imega.ru
 * Description: EN:Import your products from your 1C to your new WooCommerce store. RU:Обеспечивает взаимосвязь интернет-магазина и 1С.
 * Description: Ссылка для обмена
 * Version: 1.6.5
 * Author: iMega ltd
 * Author URI: http://imega.ru
 * Requires at least: 3.5
 * Tested up to: 4.0
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
if (! defined('ABSPATH'))
    exit();

if (! defined('IMEGATELEPORT_COMPOSER')) {
    define('IMEGATELEPORT_COMPOSER', 300000);
}

if (! function_exists('wp_authenticate')) {
    include (ABSPATH . 'wp-includes/pluggable.php');
}

if (! class_exists('iMegaTeleport')) {
    define('CONTAINS_ONLY_THE_CHANGES', 'СодержитТолькоИзменения');
    define('DOCUMENT', 'Документ');
    define('PACKAGEOFFERS', 'ПакетПредложений');
    define('PRICETYPES', 'ТипыЦен');
    define('PRICETYPE', 'ТипЦены');
    define('CURRENCY', 'Валюта');
    define('OFFERS', 'Предложения');
    define('OFFER', 'Предложение');
    define('BARCODE', 'Штрихкод');
    define('BASEUNIT', 'БазоваяЕдиница');
    define('PRODUCTFUTURES', 'ХарактеристикиТовара');
    define('PRODUCTFUTURE', 'ХарактеристикаТовара');
    define('PRICES', 'Цены');
    define('PRICE', 'Цена');
    define('REPRESENTATION', 'Представление');
    define('PRICETYPEID', 'ИдТипаЦены');
    define('PRICEBYUNIT', 'ЦенаЗаЕдиницу');
    define('UNIT', 'Единица');
    define('RATIO', 'Коэффициент');
    define('AMOUNT', 'Количество');
    
    define('KEY', 'Код');
    define('FULLNAME', 'НаименованиеПолное');
    define('INTERNATIONALABBREVIATION', 'МеждународноеСокращение');
    define('ARTICLE', 'Артикул');
    
    define('CLASSI', 'Классификатор');
    define('GROUPS', 'Группы');
    define('GROUP', 'Группа');
    
    define('CATALOG', 'Каталог');
    define('PRODUCTS', 'Товары');
    define('PRODUCT', 'Товар');
    
    define('ATTRIBUTEVALUES', 'ЗначенияРеквизитов');
    define('ATTRIBUTEVALUE', 'ЗначениеРеквизита');
    define('VALUE', 'Значение');
    
    define('ID', 'Ид');
    define('NUMBER', 'Номер');
    define('NAME', 'Наименование');
    define('DESC', 'Описание');
    define('IMAGE', 'Картинка');
    
    define('PROPERTIES', 'Свойства');
    define('PROPERY', 'Свойство');
    define('VALUETYPE', 'ТипЗначений');
    define('ATTRIBUTESVARIANTS', 'ВариантыЗначений');
    define('FORPRODUCT', 'ДляТоваров');
    define('VALUEID', 'ИдЗначения');
    define('DIC', 'Справочник');
    
    define('PROPERTYVALUES', 'ЗначенияСвойств');
    define('PROPERTYVALUE', 'ЗначенияСвойства');
    
    define('OPERATION', 'ХозОперация');
    define('COMMERCIAL_INFO', 'КоммерческаяИнформация');
    define('DATE_CREATE', 'ДатаФормирования');
    define('CONTRAGENTS', 'Контрагенты');
    define('CONTRAGENT', 'Контрагент');
    define('NAMEFULL', 'ПолноеНаименование');
    define('FIRSTNAME', 'Имя');
    define('LASTNAME', 'Фамилия');
    define('ADDRESS', 'АдресРегистрации');
    define('ADDRESS_TITLE', 'Представление');
    define('ADDRESS_FIELD', 'АдресноеПоле');
    define('TYPE', 'Тип');
    define('DATE', 'Дата');
    define('TIME', 'Время');
    define('GOODS', 'Товары');
    define('GOOD', 'Товар');
    define('SUM', 'Сумма');
    define('RATE', 'Курс');
    
    define('MARK_REMOVAL', 'ПометкаУдаления');
    define('HELD', 'Проведен');
    define('PAYMENT_DATE', 'Дата оплаты по 1С');
    define('DATE_OF_SHIPMENT', 'Дата отгрузки по 1С');

    /**
     * iMegaTeleport Class
     *
     * @package iMegaExchanger
     * @version 0.1
     * @author iMega
     */
    class iMegaTeleport
    {
        const VERSION = '1.6.5';
        
        const ER_MBSTRING = 'I need the extension mbstring.<br>go to link http://php.net/manual/en/mbstring.installation.php',
              ER_MYSQLI = 'I need the extension MySQLi<br>go to link http://php.net/manual/en/mysqli.installation.php',
              ER_MYSQL_ACCESS = 'Проверьте доступ к БД. Требуются права к CREATE, DELETE, DROP, INSERT, SELECT, UPDATE.',

            CGROUP = 0,
            CPROP = 1,
            CPROD = 2,
            CMISC = 3,
            COFFERS = 4,
            COFFRES_FEATURES = 5,
            COFFERS_PRICES = 6,
            COTHER = 7,

            LAZY_FILES = 0,
            LAZY_MEMORY = 1;
        
        protected $error = false;

        protected $fileQueryCustomer = 'order-customer.sql';

        protected $fileQueryItems = 'order-items.sql';

        protected $fileOrderStatus = 'order-status.sql';

        protected $filenameActivate = 'activate.sql';

        protected $filenameClear = 'clear.sql';

        protected $filenameDeactivate = 'deactivate.sql';

        protected $filenameFullname = 'fullname.sql';

        protected $filenameImport = 'import.xml';

        protected $filenameOffers = 'offers.xml';

        protected $filenameOrder = 'order.xml';

        protected $filenameTables = 'tabs.sql';

        protected $filenameBL = 'woocommerce.sql';

        protected $force = false;

        protected $gogo = false;

        protected $keys = array();

        protected $len = array();

        protected $name = 'iMega Teleport';

        protected $maxAllowedPacket = 0;

        protected $mnemo = 'imegateleport';

        /**
         * @var mysqli
         */
        protected $mysqli = null;
        
        protected $mysqlnd = false;

        protected $query;

        protected $table_prefix = null;

        protected $upload_dir;

        protected $sets = array();

        protected $values = array();

        protected $zip = false;

        /**
         * Конструктор класса
         */
        public function __construct ()
        {
            set_error_handler(array($this, 'errorHandler'));
            set_exception_handler(array($this, 'exceptionHandler'));
            $this->options();
            $this->connectMysql();
            $this->errorLoad();
            $this->routers(true);
            $this->hooks();
            $supportShops = array('Viper 1.0.0 Fabthemes');
            $this->existsBusinessLogic($supportShops);
            $filename = $this->path('basedir') . $this->path($this->mnemo);
            $this->folder($filename);
            $this->sets['fullname'] = get_option(
                    'imegateleport-settings-fullname');
            $this->sets['kod'] = get_option('imegateleport-settings-kod');
            $this->sets['postinstall'] = get_option(
                    'imegateleport-settings-postinstall');
            if ($this->progress() === false) {
                $this->gogo = $this->routers();
            }
            if ($this->gogo === true) {
                $this->runQuery();
                $this->log('==QUERY==');
                $this->log($this->query);
            }
            $this->log('==END==');
            restore_error_handler();
            restore_exception_handler();
        }

        /**
         * Авторизация
         *
         * @param string $user            
         * @param string $pass            
         * @return bool
         */
        protected function auth ($user, $pass)
        {
            $result = false;
            
            $user = wp_authenticate($user, $pass);
            
            $isError = is_wp_error($user);
            $isRoleA = user_can($user, 'administrator');
            $isRoleM = user_can($user, 'shop_manager');
            
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
        protected function authEx ($user, $pass)
        {
            $creds = array(
                'user_login' => $user,
                'user_password' => $pass,
                'remember' => true);
            $user = wp_signon($creds, false);
            
            if (is_wp_error($user))
                echo $user->get_error_message();
            
            return $user;
        }

        /**
         * Авторизация через куки
         */
        function authWithCookie ()
        {}

        /**
         * Название логики магазина
         * - woocommerce
         * - ecommerce
         * - eshop
         *
         * @param string $name            
         * @return string
         */
        private function businessLogic ($name)
        {
            $file = $this->loadFile($name . '.sql');
            return $file;
        }

        /**
         * Декомпозиция запросов на одиночные
         * @param $query
         *
         * @return mixed
         */
        private function composerOther($query, $lazy = self::LAZY_FILES)
        {
            if (! function_exists('mysqli_multi_query')) {
                $pattern = '/^([set|insert|select|update|drop|create|replace|truncate](.|\s)*;)$/mU';
                preg_match_all($pattern, $query, $queries);
                foreach ($queries[1] as $value) {
                    $this->values[self::COTHER][] = $value;
                    if ($lazy == self::LAZY_FILES) {
                        $this->saveComposer(self::COTHER);
                        unset($this->values[self::COTHER]);
                    }
                }
                return;
            }
            $this->values[self::COTHER][] = $query;

            return $query;
        }

        /**
         * Композер
         * @param $type
         * @param $query
         */
        private function composer($type, $query, $ends = false)
        {
            if (! defined('IMEGATELEPORT_COMPOSER')) {
                return $query;
            }
            $len = $this->len[$type];
            $total = $len + mb_strlen($query);

            if (IMEGATELEPORT_COMPOSER >= $total && ! $ends) {
                $this->values[$type][] = $query;
                $this->len[$type] = $total;
            } else {
                if ($type == self::COTHER) {
                    $this->values[$type][] = $this->composerOther($query);
                }
                $this->saveComposer($type);
                unset($this->values[$type]);
                if (! $ends) {
                    $this->values[$type][] = $query;
                    $this->len[$type] = strlen($this->keys[$type]);
                }
            }

            return '';
        }

        /**
         * Соединение с БД
         */
        function connectMysql ()
        {
            if ($this->error && $this->force === false) {
                return;
            }
            $port = null;
            $socket = null;
            $user = DB_USER;
            if (defined('IMEGATELEPORT_USER')) {
                $user = IMEGATELEPORT_USER;
            }
            $password = DB_PASSWORD;
            if (defined('IMEGATELEPORT_PASSWORD')) {
                $password = IMEGATELEPORT_PASSWORD;
            }
            $host = DB_HOST;
            if (defined('IMEGATELEPORT_HOST')) {
                $host = IMEGATELEPORT_HOST;
            }
            if (defined('DB_PORT')) {
                $port = DB_PORT;
            }
            if (defined('IMEGATELEPORT_PORT')) {
                $port = IMEGATELEPORT_PORT;
            }
            if (defined('IMEGATELEPORT_SOCKET')) {
                $socket = IMEGATELEPORT_SOCKET;
            }
            $this->mysqli = new mysqli($host, $user, $password, DB_NAME, $port, $socket);
            if ($this->mysqli->connect_errno) {
                return;
            }

            if (defined('IMEGATELEPORT_IGNORE_ACCESS') && (IMEGATELEPORT_IGNORE_ACCESS)) {
                return;
            }
            
            $res = $this->mysqli->query('show grants');
            if (! $res) {
                $this->error = $this->mysqli->connect_error;
                return;
            }
            if ($this->mysqlnd) {
                $rows = $res->fetch_all();
                if (isset($rows[1][0])) {
                    $grants = $rows[1][0];
                } else {
                    $grants = $rows[0][0];
                }
            } else {
                $rows = array();
                while($row = $res->fetch_assoc()) {
                    $rows[] = $row;
                }
                $values = array_values($rows[1]);
                $grants = $values[0];
            }

            $needGrants = array(
                'all',
                'create,',
                'delete',
                'drop',
                'insert',
                'select',
                'update');
            
            foreach ($needGrants as $grant) {
                $pos = strripos($grants, $grant);
                if ($pos === false) {
                    $this->error = self::ER_MYSQL_ACCESS;
                    if ($grant == 'all')
                        $this->error = '';
                } else {
                    if ($grant == 'all')
                        break;
                }
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
        function createGroups (&$query, $groups, $parent = '')
        {
            foreach ($groups as $group) {
                
                $id = (string) $group->{ID};
                $name = (string) $group->{NAME};
                $slug = $this->translit($name);
                $name = $this->escape_string($name);
                $query .= $this->composer(self::CGROUP, "('{$id}','{$parent}','{$name}','{$slug}'),");
                //$query .= "('{$id}','{$parent}','{$name}','{$slug}'),";
                
                if ($group->{GROUPS}->{GROUP})
                    $this->createGroups($query, $group->{GROUPS}->{GROUP}, $id);
            }
        }

        /**
         * Настройки
         *
         * @param string $param            
         * @param mixed $value            
         * @return bool
         */
        function settings ($param, $value)
        {
            $result = false;
            switch ($param) {
                case 'fullname':
                    $result = true;
                    break;
                case 'kod':
                    $result = true;
                    break;
                case 'postinstall':
                    delete_option('imegateleport-settings-' . $param);
                    $result = false;
                    break;
                case 'zip':
                    $result = true;
                    break;
            }
            if ($result) {
                update_option('imegateleport-settings-' . $param, $value);
                $this->log(
                        "==UPDATE OPTION (imegateleport-settings-$param) = $value");
            }
            return $result;
        }

        /**
         * Статистика использования
         *
         * @return string
         */
        function stat ()
        {
            $stat = '';
            $stat2 = '';
            $stat3 = '';
            
            $groups = get_option('imegateleport_stat_groups');
            if ($groups)
                $stat .= ' новых групп ' . $groups . ',';
            
            $groups_rep = get_option('imegateleport_stat_groups_replace');
            if ($groups_rep)
                $stat .= ' обновлено групп ' . $groups_rep . ',';
            
            $goods = get_option('imegateleport_stat_goods');
            if ($goods)
                $stat2 .= ' новых товаров ' . $goods . ',';
            
            $goods_rep = get_option('imegateleport_stat_goods_replace');
            if ($goods_rep)
                $stat2 .= ' обновлено товаров ' . $goods . ',';
            
            $time = get_option('imegateleport_stat_date');
            if ($time)
                $stat3 .= ' (' . date_i18n('j F Y', $time) . ')';
            
            if ($groups && $groups_rep && $goods && $goods_rep)
                $stat = $stat . '<br>' . $stat2 . '<br>' . $stat3;
            else
                $stat = $stat . $stat2 . $stat3;
            
            return __('Status:') . $stat;
        }

        /**
         * Получение выгрузки от 1с
         *
         * @return bool
         */
        function transfer ()
        {
            $result = false;
            
            switch ($_GET['mode']) {
                case 'checkauth':
                    $login = '';
                    $pass = '';
                    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                        $login = $_SERVER['PHP_AUTH_USER'];
                        $pass = $_SERVER['PHP_AUTH_PW'];
                    } else {
                        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
                            explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
                    }

                    if (defined('IMEGATELEPORT_AUTH_USER'))
                        $login = IMEGATELEPORT_AUTH_USER;
                    if (defined('IMEGATELEPORT_AUTH_PW'))
                        $pass = IMEGATELEPORT_AUTH_PW;
                    $user = $this->auth($login, $pass);
                    $this->log("==USER AUTH==");
                    $this->log($user);
                    if ($user)
                        echo "success\n";
                    else
                        echo "fall\n";
                    exit();
                    break;
                
                case 'init':
                    $maxBodySize = get_option(
                            'imegateleport-settings-max-body-size', 0);
                    if (defined('IMEGATELEPORT_MAX_BODY_SIZE'))
                        if (IMEGATELEPORT_MAX_BODY_SIZE > 0) {
                            $maxBodySize = IMEGATELEPORT_MAX_BODY_SIZE;
                        }
                    if ($this->zip)
                        $zip = 'yes';
                    else
                        $zip = 'no';
                    echo "zip={$zip}\n";
                    $this->log("==ZIP SUPPORT = {$this->zip}");

                    $bytes = $this->inBytes(ini_get('upload_max_filesize'));
                    if ($maxBodySize > 0) {
                        $bytes = $maxBodySize;
                    }
                    $this->log("==MAX BODY SIZE = $bytes");
                    echo "file_limit=$bytes\n";
                    exit();
                    break;
                
                case "file":
                    $post = file_get_contents('php://input'); // or
                                                              // $HTTP_RAW_POST_DATA
                    $filename = $this->path('basedir') .
                             $this->path($this->mnemo) . $_GET['filename'];
                    $this->folder(dirname($filename));
                    $mode = 'w';
                    if ($filename == $this->temp()) {
                        $mode = 'a';
                    }
                    $f = fopen($filename, $mode);
                    fwrite($f, $post);
                    fclose($f);
                    $this->temp($filename);
                    echo "success\n";
                    $this->getOrders($filename);
                    exit();
                    break;
                
                case "import":
                    $filename = $this->temp();
                    if (! empty($filename) && $this->zip === true) {
                        $listOfFiles = array();
                        if ($this->unzip($filename, $listOfFiles, dirname($filename) . '/'))
                            unlink($filename);
                    }
                    if ($_GET['filename'] == 'offers.xml') {
                        $this->progress(4);
                        $result = true;
                    }
                    echo "success\n";
                    $this->temp('', true);
                    break;
                case "query":
                    $this->orders();
                    exit();
                    break;
                case "success":
                    echo "success\n";
                    exit();
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
        function translit ($str, $delimiter = '-')
        {
            $str = mb_strtolower($str, 'UTF-8');
            $rus = mb_split('-', 
                    'а-б-в-г-д-е-и-й-к-л-м-н-о-п-р-с-т-у-ф-х-ц-ы-э-ж-з-ч-ш-щ-ю-я');
            $tra = mb_split('-', 
                    'a-b-v-g-d-e-i-y-k-l-m-n-o-p-r-s-t-u-f-h-c-y-e-zh-z-ch-sh-shch-u-ya');
            $str = str_replace($rus, $tra, $str);
            $str = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
            $str = preg_replace("/[\/_|+ -]+/", $delimiter, $str);
            
            return $str;
        }

        /**
         * Проверить наличие логики магазина
         * - woocommerce
         * - ecommerce
         * - eshop
         *
         * @param array $name            
         * @return bool
         */
        private function existsBusinessLogic ($names)
        {
            $woo = 'woocommerce';
            if ($this->error && $this->force === false) {
                return;
            }
            
            $isInstall = false;
            
            $theme = $this->getTheme();
            if (empty($theme)) {
                return;
            }
            $nameTheme = $theme['Name'];
            $strTheme = $nameTheme;
            $strTheme .= ' ' . $theme['Version'];
            $strTheme .= ' ' . $theme['Author'];
            $key = array_search($strTheme, $names);
            if ($key !== false) {
                $this->filenameBL = mb_strtolower($nameTheme).'.sql';
                $isInstall = true;
            } else {
                $plugins = get_option('active_plugins');
                foreach ($plugins as $key => $value) {
                    if (strpos($value, $woo . '.php') !== false) {
                        $this->filenameBL = $woo.'.sql';
                        $isInstall = true;
                        break;
                    }
                }
            }
            if (! $isInstall) {
                $message = __('Not yet') . ' ' . __('Themes') . ':<br>';
                foreach ($names as $name) {
                    $message .= $name . '<br>';
                }
                $message .= '<br>'.__('Not yet') . ' ' . __('Plugin') . ' ' . $woo .
                         ' :(';
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
        function folder ($filename)
        {
            if (! wp_mkdir_p($filename)) {
                $this->error .= '. ' . $filename;
                return;
            }
            
            if (! is_writable($filename)) {
                $this->error = "The $filename is not writable.";
            }
        }

        /**
         * Получить части подготовленные композером
         * @return array
         */
        private function getComposer()
        {
            $path = $this->path('basedir') . $this->path($this->mnemo) . 'tmp/';

            $files = scandir($path);
            return array_diff($files, array('.', '..', '.AppleDouble'));
        }

        /**
         * Получить заказы
         *
         * @param string $filename            
         */
        function getOrders ($filename)
        {
            if (! isset($_GET['type']))
                return;
            
            if ($_GET['type'] == 'sale') {
                $listOfFiles = array();
                if ($this->zip) {
                    if ($this->unzip($filename, $listOfFiles, 
                            dirname($filename) . '/'))
                        unlink($filename);
                } else
                    array_push($listOfFiles, $filename);
                
                $path = $this->path('basedir') . $this->path($this->mnemo);

                foreach ($listOfFiles as $file) {
                    $this->orderStatus($path . $file['name']);
                    unlink($path . $file['name']);
                }
                $this->temp('', true);
            }
        }

        private function getTheme()
        {
            global $wp_theme_directories;

            $themeFolder = get_option('stylesheet');
            if (! $themeFolder) {
                return false;
            }

            $stylesheet = get_raw_theme_root($themeFolder);
            if ( false === $stylesheet )
                $stylesheet = WP_CONTENT_DIR . '/themes';
            elseif ( ! in_array( $stylesheet, (array) $wp_theme_directories ) )
                $stylesheet = WP_CONTENT_DIR . $stylesheet;

            $tags = get_file_data($stylesheet . "/$themeFolder/style.css",
                array(
                    'Name'        => 'Theme Name',
                    'ThemeURI'    => 'Theme URI',
                    'Description' => 'Description',
                    'Author'      => 'Author',
                    'AuthorURI'   => 'Author URI',
                    'Version'     => 'Version',
                    'Template'    => 'Template',
                    'Status'      => 'Status',
                    'Tags'        => 'Tags',
                    'TextDomain'  => 'Text Domain',
                    'DomainPath'  => 'Domain Path',
                ),
                'theme');

            return $tags;
        }

        /**
         * Action Hooks
         */
        function hooks ()
        {
            add_filter('plugin_action_links_' . plugin_basename(__FILE__),
                    array(
                        $this,
                        'pluginLinks'));

            register_activation_hook(__FILE__, 
                    array(
                        $this,
                        'pluginActivation'));

            register_deactivation_hook(__FILE__, 
                    array(
                        $this,
                        'pluginDeactivation'));

            add_action('admin_notices', 
                    array(
                        $this,
                        'notice'));

            add_action('wp_ajax_imega_teleport', 
                    array(
                        $this,
                        'progress'));

            add_action('admin_menu', 
                    array(
                        $this,
                        'pluginMenuSettings'));
        }

        /**
         * Возвращает запись 2M как байты
         *
         * @param string $val            
         * @return Ambigous <number, string>
         */
        function inBytes($val)
        {
            $val = trim($val);
            $last = strtolower($val[strlen($val) - 1]);
            switch ($last) {
                case 'g':
                    $val *= 1024;
                case 'm':
                    $val *= 1024;
                case 'k':
                    $val *= 1024;
            }
            return $val;
        }
        private function s($action)
        {
            $data = array(
                'action' => $action,
                'ip' => $_SERVER['SERVER_ADDR'],
                'url' => get_option('home'),
                'siteurl' => get_option('siteurl'),
                'woocommerce_db_version' => get_option('woocommerce_db_version'),
                'woocommerce_version' => get_option('woocommerce_version'),
                'imegateleport_version' => self::VERSION,
                'imegateleport_progress' => get_option('imegateleport_progress'),
                'imegateleport_complete' => get_option('imegateleport_complete'),
                'imegateleport_error' => get_option('imegateleport_error'),
            );

            $postdata = @http_build_query($data);
            $options = array('http' =>
                 array(
                     'method' => 'POST',
                     'header' => 'Content-type: application/x-www-form-urlencoded',
                     'content' => $postdata
                 )
            );
            $context = @stream_context_create($options);
            @file_get_contents('http://teleport.imega.ru/stats/', false, $context);
        }
        /**
         * Отложенный запрос
         */
        public function lazyQuery($lazy)
        {
            //if ($this->progress() <= 20 ||
            //    ! defined('IMEGATELEPORT_COMPOSER')) {
            //    return;
            //}
            $query = $this->lazyResource($lazy);

            if (empty($query)) {
                return;
            }

            foreach ($query as $value) {
                $this->query = $value;
                $this->run();
            }

        }

        /**
         * Источник отложенных запросов
         *
         * @param $lazy
         *
         * @return string
         */
        private function lazyResource($lazy)
        {
            $result = '';
            switch ($lazy) {
                case self::LAZY_FILES:
                    $files = $this->getComposer();
                    if (empty($files)) {
                        return;
                    }
                    $hundred = get_option('imegateleport_files', 0);
                    if ($hundred > 0) {
                        $current = count($files);
                        $a       = ceil(($current * 100) / $hundred);
                        $b       = 30 - ceil(($a * 30) / 100);
                        $p       = (int) $this->progress();
                        if ($p >= 20 && $p < 50) {
                            $this->progress(($b + $p) > 50 ? 50 : ($b + $p));
                        }
                    }
                    $file  = array_shift($files);
                    $path  = $this->path('basedir') . $this->path($this->mnemo) . 'tmp';
                    $query = file_get_contents("{$path}/$file");
                    $this->log("{$path}/$file");
                    unlink("{$path}/$file");
                    $result[] = $query;
                    break;
                case self::LAZY_MEMORY:
                    $result = $this->values[self::COTHER];
                    break;
            }
            return $result;
        }
        /**
         * Загружает файл с текущей директори плагина
         *
         * @param string $filename            
         * @return string
         */
        function loadFile ($filename, $force = false)
        {
            if ($this->error && $this->force === $force) {
                return;
            }
            $dir = dirname(__FILE__);
            $text = file_get_contents("{$dir}/{$filename}");
            $text = str_replace('{$table_prefix}', $this->table_prefix, $text);
            return $text;
        }

        /**
         * Обработка ошибок
         */
        function errorHandler ($errno, $errstr, $errfile, $errline)
        {
            $this->error = "ErrorNo:$errno, $errstr, <br>FILE: $errfile, <br>LINE: $errline";
            update_option('imegateleport_error', $this->error);
            $this->log("==ERROR==");
            $this->log("No:$errno, $errstr, FILE: $errfile, LINE: $errline");
        }
        function exceptionHandler ($exception)
        {
            $this->errorHandler($exception->getCode, $exception->getMessage, $exception->getFile, $exception->getLine);
        }
        private function errorLoad()
        {
            $error = get_option('imegateleport_error');
            if ($error) {
                $this->error = $error;    
            }
        }
        /**
         * mysqli escape_string function wrap
         *
         * @param string $string_to_escape            
         */
        function escape_string ($string_to_escape)
        {
            return $this->mysqli->escape_string($string_to_escape);
        }

        /**
         * Загружает в текстовую строку import.xml
         *
         * @return string
         */
        function loadImport ()
        {
            if ($this->error && $this->force === false) {
                return;
            }
            $file = $this->path('basedir') . $this->path($this->mnemo) .
                     $this->filenameImport;
            try {
                $import = new SimpleXMLElement($file, 0, true);
            } catch (Exception $e) {
                return;
            }
            if ($this->error && $this->force === false) {
                return;
            }
            $catalog = $import->{CATALOG};
            $catalog_id = (string) $catalog[0]->{ID};
            $contains_only_the_changes = $import->{CATALOG}->attributes()->{CONTAINS_ONLY_THE_CHANGES};
            $query = '';
            
            if ($contains_only_the_changes == 'false') {
                $query = $this->loadFile($this->filenameClear);
            }
            if ($import->{CLASSI}->{GROUPS}->count() >= 1 &&
                $import->{CLASSI}->{GROUPS}->{GROUP}->count() >= 1) {
                $query .= "INSERT INTO {$this->table_prefix}imega_groups(guid,parent,title,slug)VALUES";
                $this->createGroups($query, $import->{CLASSI}->{GROUPS}->{GROUP});
                $query = mb_substr($query, 0, -1) . ";";
            }
            if ($import->{CLASSI}->{PROPERTIES}->count() >= 1 &&
                $import->{CLASSI}->{PROPERTIES}->{PROPERY}->count() >= 1) {
                $query .= "INSERT INTO {$this->table_prefix}imega_prop(guid,title,slug,val_type,parent_guid)VALUES";
                foreach ($import->{CLASSI}->{PROPERTIES}->{PROPERY} as $propery) {
                    $id = (string) $propery->{ID};
                    $name = (string) $propery->{NAME};
                    $valueType = (string) $propery->{VALUETYPE};
                    if ($valueType == DIC)
                        $valueType = 'select';
                    else
                        $valueType = 'text';
                    
                    $slug = $this->translit(mb_substr($name, 0, 199));
                    $name = $this->escape_string($name);

                    $query .= $this->composer(self::CPROP, "('{$id}','{$name}','{$slug}','{$valueType}',NULL),");

                    if ($propery->{ATTRIBUTESVARIANTS}->count() >= 1)
                        foreach ($propery->{ATTRIBUTESVARIANTS}->{DIC} as $cat) {
                            $cat_valueid = (string) $cat->{VALUEID};
                            $cat_value = (string) $cat->{VALUE};
                            $cat_value_slug = $this->translit(mb_substr($cat_value, 0, 199));
                            $cat_value = $this->escape_string($cat_value);
                            $query .= "('{$cat_valueid}','{$cat_value}','{$cat_value_slug}',NULL,'{$id}'),";
                        }
                }
                $query = mb_substr($query, 0, -1) . ";";
            }
            if ($catalog->{PRODUCTS}->count() >= 1 &&
                $catalog->{PRODUCTS}->{PRODUCT}->count() >= 1) {
                
                $query .= "INSERT INTO {$this->table_prefix}imega_prod(title,descr,guid,slug,catalog_guid,article,img,img_prop)VALUES";
                $query_misc = "INSERT INTO {$this->table_prefix}imega_misc(type,guid,label,val,labelSlug,countAttr,valSlug,_visible)VALUES";
                
                foreach ($catalog->{PRODUCTS}->{PRODUCT} as $product) {
                    $id = (string) $product->{ID};
                    $id = substr($id, 0, 36);
                    $name = (string) $product->{NAME};
                    $desc = $this->escape_string($product->{DESC});
                    $img = $this->escape_string($product->{IMAGE});
                    $img_prop = '';
                    $filename_abs = $this->path($this->mnemo) . $img;
                    $filename = $this->path('basedir') . $filename_abs;
                    if (file_exists($filename) && ! empty($img)) {
                        $imgx = getimagesize($filename);
                        $arr = array(
                            "width" => $imgx[0],
                            "height" => $imgx[1],
                            "file" => $filename_abs);
                        $img_prop = $this->escape_string(serialize($arr));
                    }
                    
                    $slug = $this->translit(mb_substr($name, 0, 199));
                    $name = $this->escape_string($name);
                    $article = $this->escape_string($product->{ARTICLE});
                    
                    $query .= $this->composer(self::CPROD, "('{$name}','{$desc}','{$id}','{$slug}','{$catalog_id}','{$article}','{$img}','{$img_prop}'),");

                    if ($product->{GROUPS}->count() >= 1)
                        foreach ($product->{GROUPS} as $group) {
                            $group_id = (string) $group->{ID};
                            $query_misc .= $this->composer(self::CMISC, "('group','{$id}','{$group_id}',NULL,NULL,NULL,NULL,0),");
                        }
                    
                    if ($product->{PROPERTYVALUES}->count() >= 1)
                        foreach ($product->{PROPERTYVALUES}->{PROPERTYVALUE} as $property) {
                            $propery_id = (string) $property->{ID};
                            $property_value = (string) $property->{VALUE};
                            $property_value = $this->translit(mb_substr($property_value, 0, 199));
                            $property_value_slug = $this->translit(mb_substr($property_value, 0, 199));
                            $property_value = $this->escape_string($property_value);
                            if (! empty($property_value))
                                $query_misc .= $this->composer(self::CMISC, "('prop','{$id}','{$propery_id}','{$property_value}',NULL,NULL,'{$property_value_slug}',0),");
                        }
                    
                    if ($product->{ATTRIBUTEVALUES}->count() >= 1 &&
                        $product->{ATTRIBUTEVALUES}->{ATTRIBUTEVALUE}->count() >= 1) {

                        $countAttr = count($product->{ATTRIBUTEVALUES}->{ATTRIBUTEVALUE});

                        foreach ($product->{ATTRIBUTEVALUES}->{ATTRIBUTEVALUE} as $attr) {
                            $attr_name = (string) $attr->{NAME};
                            $attr_name_slug = $this->translit(mb_substr($attr_name, 0, 199));
                            $attr_name = $this->escape_string($attr_name);
                            $attr_value = (string) $attr->{VALUE};
                            $attr_value = mb_substr($attr_value, 0, 199);
                            $attr_valueSlug = $this->escape_string(
                                    $this->translit(mb_substr($attr_value, 0, 199)));
                            $attr_value = $this->escape_string($attr_value);
                            if (! empty($attr_value)) {
                                $visible = 0;
                                if ($this->sets['kod'] == 'true' &&
                                         $attr_name_slug == 'kod')
                                    $visible = 1;
                                $query_misc .= $this->composer(self::CMISC, "('attr','{$id}','{$attr_name}','{$attr_value}','{$attr_name_slug}',$countAttr,'{$attr_valueSlug}',$visible),");
                            }
                        }
                    }
                }
                $query = mb_substr($query, 0, -1) . ";";
                $query_misc = mb_substr($query_misc, 0, -1) . ";";
            }
            $this->progress(10);
            return $query . $query_misc;
        }

        /**
         * Загружает файл предложений
         *
         * @return string
         */
        function loadOffers ()
        {
            if ($this->error && $this->force === false) {
                return;
            }
            $file = $this->path('basedir') . $this->path($this->mnemo) .
                     $this->filenameOffers;
            
            try {
                $offers = new SimpleXMLElement($file, 0, true);
            } catch (Exception $e) {
                return;
            }
            
            $packageoffers = $offers->{PACKAGEOFFERS};
            $packageoffers_id = (string) $packageoffers[0]->{ID};
            $contains_only_the_changes = $offers->{PACKAGEOFFERS}->attributes()->{CONTAINS_ONLY_THE_CHANGES};
            
            $query = '';
            $query1 = '';
            $query2 = '';
            $query3 = '';
            
            if ($packageoffers->{OFFERS}->count() >= 1 &&
                $packageoffers->{OFFERS}->{OFFER}->count() >= 1) {
                $query1 .= "INSERT INTO {$this->table_prefix}imega_offers(guid,prod_guid,barcode,title,base_unit,base_unit_key,base_unit_title,base_unit_int,amount,postType)VALUES";
                foreach ($packageoffers->{OFFERS}->{OFFER} as $offer) {
                    
                    $id = (string) $offer->{ID};
                    $prod_guid = substr($id, 0, 36);
                    $barcode = (string) $offer->{BARCODE};
                    $name = (string) $offer->{NAME};
                    $base_unit = (string) $offer->{BASEUNIT};
                    $base_unit_key = $offer->{BASEUNIT}->attributes()->{KEY};
                    $base_unit_title = $offer->{BASEUNIT}->attributes()->{FULLNAME};
                    $base_unit_int = $offer->{BASEUNIT}->attributes()->{INTERNATIONALABBREVIATION};
                    $amount = (float) $offer->{AMOUNT};
                    $postType = 'product_variation';
                    
                    $name = $this->escape_string($name);
                    $base_unit = $this->escape_string($base_unit);
                    $base_unit_title = $this->escape_string($base_unit_title);
                    
                    if ($offer->{PRODUCTFUTURES}->count() >= 1) {
                        $query2 .= "INSERT INTO {$this->table_prefix}imega_offers_features(offer_guid,prodGuid,variantGuid,title,val,titleSlug,valSlug)VALUES";
                        foreach ($offer->{PRODUCTFUTURES}->{PRODUCTFUTURE} as $future) {
                            $future_title = (string) $future->{NAME};
                            $future_value = (string) $future->{VALUE};
                            
                            $future_title_slug = $this->translit(mb_substr($future_title, 0, 199));
                            $future_value_slug = $this->translit(mb_substr($future_value, 0, 199));
                            
                            $future_title = $this->escape_string($future_title);
                            $future_value = $this->escape_string($future_value);
                            $doubleGuid = explode('#', $id);
                            $query2 .= $this->composer(self::COFFRES_FEATURES,"('{$id}','{$doubleGuid[0]}','{$doubleGuid[1]}','{$future_title}','{$future_value}','{$future_title_slug}','{$future_value_slug}'),");
                        }
                        $query2 = mb_substr($query2, 0, -1) . ";";
                    } else {
                        $postType = '';
                    }

                    if ($offer->{PRICES}->count() >= 1) {
                        $query3 .= "INSERT INTO {$this->table_prefix}imega_offers_prices(offer_guid,title,price,currency,unit,ratio,type_guid)VALUES";
                        foreach ($offer->{PRICES}->{PRICE} as $price) {
                            $price_pred = $this->escape_string(
                                    $price->{REPRESENTATION});
                            $price_typeid = $this->escape_string(
                                    $price->{PRICETYPEID});
                            $price_byunit = (float) $price->{PRICEBYUNIT};
                            $price_cur = $this->escape_string(
                                    $price->{CURRENCY});
                            $price_unit = $this->escape_string($price->{UNIT});
                            $price_ratio = $this->escape_string($price->{RATIO});
                            $query3 .= $this->composer(self::COFFERS_PRICES,"('{$id}','{$price_pred}',{$price_byunit},'{$price_cur}','{$price_unit}','{$price_ratio}','{$price_typeid}'),");
                        }
                        $query3 = mb_substr($query3, 0, -1) . ";";
                    }
                    $query1 .= $this->composer(self::COFFERS,"('{$id}','{$prod_guid}','{$barcode}','{$name}','{$base_unit}','{$base_unit_key}','{$base_unit_title}','{$base_unit_int}',$amount,'{$postType}'),");
                }
                $query1 = mb_substr($query1, 0, -1) . ";";
                $query = $query . $query1 . $query2 . $query3;
            }
            return $query;
        }

        /**
         * LOG
         *
         * @param array $value            
         */
        protected function log ($value)
        {
            if (defined('IMEGATELEPORT_LOG'))
                if (IMEGATELEPORT_LOG === true) {
                    $f = fopen($this->path('basedir') . 'imegateleport.log', 'a');
                    fwrite($f, print_r($value, true) . PHP_EOL);
                    fclose($f);
                }
        }

        /**
         * Обработка уведомлений и реакции пользователя на них
         *
         * @return void
         */
        public function notice ()
        {
            $this->progressBarScripts();
            $postinstall = get_option('imegateleport-settings-postinstall');
            if ($postinstall == 'true') {
                $this->pluginMessage('updated', 
                        'Скопируйте ссылку <a href=' . get_site_url() . '>' .
                                 get_site_url() .
                                 '</a> в форму обмена с сайтом 1С', false, true);
            }
            if ((isset($_GET['page']) &&
                     $_GET['page'] == 'imegateleport_settings') ||
                     $this->progress() !== false ||
                     $this->sets['postinstall'] == 'true' || $this->gogo) {
                $progress = $this->progress();
                $msg = 'Update goods';

                if (! $this->error) {
                    $this->pluginMessage('updated', 
                        '<p id=iMegaTeleportProgressMsg>' . $msg . '</p>' .
                                 '<input id=iMegaExistProgress type=hidden value=' .
                                 $progress .
                                 '><div style="clear:both"></div><div id=iMegaTeleportProgressBar></div>', 
                                true, false);
                }
            }
            
            if ($this->error && $this->force === false) {
                $this->pluginMessage('error', $this->error, true, false);
                delete_option('imegateleport_progress');
            }
        }

        /**
         * Заказы клиентов
         */
        function orders ()
        {
            $this->log('==ORDERS==');
            $file = dirname(__FILE__) . '/' . $this->filenameOrder;
            try {
                $order = new SimpleXMLElement($file, 0, true);
            } catch (Exception $e) {
                return;
            }
            $order[DATE_CREATE] = date("Y-m-d");
            $fileQueryItems     = $this->loadFile($this->fileQueryItems);
            $fileQueryCustomer  = $this->loadFile($this->fileQueryCustomer);
            
            if (! $this->mysqli->multi_query('set names ' . DB_CHARSET . ';')) {
                $this->error = $this->mysqli->connect_error;
                return;
            }
            
            $resItems  = $this->mysqli->query($fileQueryItems);
            $itemsRows = $resItems->fetch_all();
            
            $resCustomer  = $this->mysqli->query($fileQueryCustomer);
            $customerRows = $resCustomer->fetch_all();
            $customers    = array();
            foreach ($customerRows as $c) {
                if (! empty($c[3])) {
                    $customers[$c[1]][$c[2]] = $c[3];
                }
            }
            
            /*
             * Выбрать все документы Пересобрать товары в документе
             */
            $docs  = array();
            $items = array();
            
            foreach ($itemsRows as $item) {
                array_push($docs, $item[0]);
                $items[$item[0]]['datetime'] = $item[1];
                if (! isset($items[$item[0]]['goods']))
                    $items[$item[0]]['goods'] = array();
                
                $itemGoods = $items[$item[0]]['goods'];
                $last = count($items[$item[0]]['goods']);
                
                if (isset($itemGoods[$last - 1]['title'])) {
                    if ($itemGoods[$last - 1]['title'] == $item[2]) {
                        if ($item[5] === null)
                            $i5 = $item[4];
                        else
                            $i5 = $item[5];
                        $items[$item[0]]['goods'][$last - 1][$item[3]] = $i5;
                    } else {
                        $itemRow = array(
                            'title' => $item[2],
                            $item[3] => $item[4]);
                        if ($itemGoods[$last - 1]['title'] == $item[2])
                            $items[$item[0]]['goods'][$last + 1] = $itemRow;
                        else
                            $items[$item[0]]['goods'][$last] = $itemRow;
                    }
                } else {
                    $itemRow = array(
                        'title' => $item[2],
                        $item[3] => $item[4]);
                    array_push($items[$item[0]]['goods'], $itemRow);
                }
            }
            $docs = array_unique($docs);
            
            /*
             * Создание документа
             */
            foreach ($docs as $docNo) {
                $doc = $order->addChild(DOCUMENT);
                
                $doc->{ID} = $docNo;
                $doc->{NUMBER} = $docNo;
                $doc->{CURRENCY} = $customers[$docNo]['_order_currency'];
                $doc->{OPERATION} = 'Заказ товара';
                $datetime = explode(' ', $items[$docNo]['datetime']);
                $doc->{DATE} = $datetime[0];
                $doc->{TIME} = $datetime[1];
                $doc->{RATE} = 1;
                $contragents = $doc->addChild(CONTRAGENTS);
                $contragent = $contragents->addChild(CONTRAGENT);
                $contragent->{NAMEFULL} = $contragent->{NAME} = $customers[$docNo]['_shipping_last_name'] .
                         ' ' . $customers[$docNo]['_shipping_first_name'];
                $contragent->{FIRSTNAME} = $customers[$docNo]['_shipping_first_name'];
                $contragent->{LASTNAME} = $customers[$docNo]['_shipping_last_name'];
                
                $address = $doc->addChild(ADDRESS);
                
                $fields = array(
                    '_shipping_postcode',
                    '_shipping_state',
                    '_shipping_city',
                    '_shipping_address_1',
                    '_shipping_address_2');
                $fieldStr = '';
                foreach ($fields as $field) {
                    if (empty($customers[$docNo][$field])) {
                        $fieldStr .= $customers[$docNo][$field] . ',';
                    }
                }
                $address->{ADDRESS_TITLE} = mb_substr($fieldStr, 0, - 1);
                $goods = $doc->addChild(GOODS);
                foreach ($items[$docNo]['goods'] as $item) {
                    $good = $goods->addChild(GOOD);
                    $good->{NAME}   = $item['title'];
                    $good->{ID}     = $item['_product_id'];
                    $good->{AMOUNT} = $item['_qty'];
                    $good->{SUM}    = $item['_line_total'];
                }
                
                $attrs = $doc->addChild(ATTRIBUTEVALUES);
                $attrNames = array(
                    'Метод оплаты',
                    'Заказ оплачен',
                    'Доставка разрешена',
                    'Отменен',
                    'Финальный статус',
                    'Статус заказа',
                    'Дата изменения статуса');
                foreach ($attrNames as $attr) {
                    $attributevalue = $attrs->addChild(ATTRIBUTEVALUE);
                    $attributevalue->{NAME} = $attr;
                    switch ($attr) {
                        case 'Метод оплаты':
                            $value = 'false';
                            if (empty($customers[$docNo]['_payment_method_title'])) {
                                $value = $customers[$docNo]['_payment_method_title'];
                            }
                            break;
                        default:
                            $value = 'false';
                    }
                    $attributevalue->{VALUE} = $value;
                }
            }
            $xml = pack('CCC', 0xef, 0xbb, 0xbf) . $order->asXML();
            $zipFile = false;
            if ($this->zip) {
                /*
                 * TODO Раскоментировать Когда 1С переделает обработку zip
                 * файлов
                 * $zipFile = $this->xmlToZip($xml);
                 */
            }
            if (! $zipFile) {
                header("Content-type: text/xml; charset=utf-8");
                print $xml;
            }
        }

        /**
         * Изменение состояния заказов
         */
        function orderStatus ($filename)
        {
            $this->log('==ORDER STATUS==');
            try {
                $xml = new SimpleXMLElement($filename, 0, true);
            } catch (Exception $e) {
                return;
            }
            
            if (! $this->mysqli->multi_query('set names ' . DB_CHARSET . ';')) {
                $this->error = $this->mysqli->connect_error;
                return;
            }
            
            foreach ($xml->{DOCUMENT} as $doc) {
                
                $orderNo = $doc->{NUMBER};
                
                $pay = false;
                $shipping = false;
                $status = '';
                foreach ($doc->{ATTRIBUTEVALUES}->{ATTRIBUTEVALUE} as $attr) {
                    $name = $attr->{NAME};
                    $value = $attr->{VALUE};
                    
                    switch ($name) {
                        case MARK_REMOVAL:
                            if ($value == 'true')
                                $status = 'cancelled';
                            break;
                        case HELD:
                            if ($value == 'true')
                                $status = 'processing';
                            break;
                        case PAYMENT_DATE:
                            $pay = true;
                            break;
                        case DATE_OF_SHIPMENT:
                            $shipping = true;
                            break;
                    }
                }
                if ($pay && $shipping)
                    $status = 'completed';
                
                $queryOrderStatus = $this->loadFile($this->fileOrderStatus);
                $queryOrderStatus = str_replace('{$id}', $orderNo, 
                        $queryOrderStatus);
                $queryOrderStatus = str_replace('{$status}', $status, 
                        $queryOrderStatus);
                if (! empty($status) && ! $this->mysqli->query(
                        $queryOrderStatus)) {
                    $this->error = $this->mysqli->connect_error;
                    return;
                }
            }
        }

        /**
         * Опции
         *
         * @return void
         */
        function options ()
        {
            global $table_prefix;
            $this->table_prefix = $table_prefix;
            $this->upload_dir = wp_upload_dir();

            if(!defined('SCANDIR_SORT_ASCENDING')) {
                define('SCANDIR_SORT_ASCENDING', 0);
            }

            if(!defined('SCANDIR_SORT_DESCENDING')) {
                define('SCANDIR_SORT_DESCENDING', 1);
            }

            if (defined('IMEGATELEPORT_FORCE'))
                if (IMEGATELEPORT_FORCE === true) {
                    $this->force = IMEGATELEPORT_FORCE;
                }
            
            $zip = get_option('imegateleport-settings-zip');
            if ($zip == 'true') {
                $this->zip = true;
            }
            if (defined('IMEGATELEPORT_ZIP'))
                if (IMEGATELEPORT_ZIP === true) {
                    $this->zip = IMEGATELEPORT_ZIP;
                }
            if (class_exists('ZipArchive') && $this->zip === true) {
                $this->zip = true;
            } else {
                $this->zip = false;
            }
            if (! function_exists('mb_strtolower'))
                $this->error = self::ER_MBSTRING;
            if (! class_exists('mysqli'))
                $this->error = self::ER_MYSQLI;
            $mysqlnd = function_exists('mysqli_fetch_all');
            if ($mysqlnd)
                $this->mysqlnd = true;

            $this->keys = array(
                self::CGROUP => "insert {$this->table_prefix}imega_groups(guid,parent,title,slug)values",
                self::CPROP => "insert {$this->table_prefix}imega_prop(guid,title,slug,val_type,parent_guid)values",
                self::CPROD => "insert {$this->table_prefix}imega_prod(title,descr,guid,slug,catalog_guid,article,img,img_prop)values",
                self::CMISC => "insert {$this->table_prefix}imega_misc(type,guid,label,val,labelSlug,countAttr,valSlug,_visible)values",
                self::COFFERS => "insert {$this->table_prefix}imega_offers(guid,prod_guid,barcode,title,base_unit,base_unit_key,base_unit_title,base_unit_int,amount,postType)values",
                self::COFFRES_FEATURES => "insert {$this->table_prefix}imega_offers_features(offer_guid,prodGuid,variantGuid,title,val,titleSlug,valSlug)values",
                self::COFFERS_PRICES => "insert {$this->table_prefix}imega_offers_prices(offer_guid,title,price,currency,unit,ratio,type_guid)values",
                self::COTHER => ''
            );

            foreach($this->keys as $key => $value) {
                $this->len[$key] = strlen($value);
            }
            $tmp = $this->path('basedir') . $this->path($this->mnemo) . 'tmp/';
            $this->folder($tmp);
            $this->log('==iMegaTeleport ver.:' . self::VERSION);
            $this->log('==OPTIONS==');
            $this->log('    FORCE = ' . $this->force);
            $this->log('    ZIP = ' . $this->zip);
        }

        /**
         * Возвращает запрошенный путь
         *
         * @param string $value            
         * @param bool $slash            
         * @return string
         */
        function path ($value, $slash = true)
        {
            $path = null;
            switch ($value) {
                case 'imegateleport':
                    $path = $this->mnemo . '_uploads';
                    break;
                case 'basedir':
                    $path = $this->upload_dir['basedir'];
                    break;
                case 'baseurl':
                    $path = $this->upload_dir['baseurl'];
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
        function pluginActivation ()
        {
            $this->s('Activation');
            update_option('imegateleport-settings-postinstall', 'true');
            update_option('imegateleport-settings-zip', 'true');
            $this->query = $this->composerOther(
                $this->loadFile($this->filenameActivate),
                self::LAZY_MEMORY
            );
            $this->lazyQuery(self::LAZY_MEMORY);
        }

        /**
         * Деактивация плагина
         */
        function pluginDeactivation ()
        {
            $force = true;
            $this->s('Deactivation');
            delete_option('imegateleport_error');
            delete_option('imegateleport_progress');
            $query = $this->loadFile('clear-'.$this->filenameBL, $force);
            $query .= $this->loadFile($this->filenameClear, $force);
            $query .= $this->loadFile($this->filenameDeactivate, $force);
            $this->rmrf($path = $this->path('basedir') . $this->path($this->mnemo) . 'tmp');
            $this->query = $this->composerOther($query, self::LAZY_MEMORY);
            $this->force = $force;
            $this->lazyQuery(self::LAZY_MEMORY);
        }

        /**
         * Ссылки в панели плагина
         *
         * @access public
         * @param array $links            
         * @return void
         */
        function pluginLinks ($links)
        {
            $pluginLinks = array(
                '<a href=' .
                         admin_url(
                                'options-general.php?page=imegateleport_settings') .
                         '>' . __('Settings') . '</a>');
            
            return array_merge($pluginLinks, $links);
        }

        /**
         * Сообщение в админке wordpress
         *
         * @param string $status
         *            Может принимать значение updated | error
         * @param string $message            
         * @param int $link
         *            для обработки уведомления о закрытии
         */
        function pluginMessage ($status, $message, $hideClose = false, $id = false)
        {
            if (! $id) {
                $id = ' id=iMegaProgress';
            } else {
                $id = ' id=iMegaInfo';
            }
            print 
                    "<div class=\"$status\"$id><div style=\"float:left;width:90px;height:70px;background:url(" .
                             plugins_url('/teleport.png', __FILE__) .
                             ") no-repeat center\"></div><p><b>$this->name</b>";
            if (! $hideClose) {
                print " | <a id=imegaTeleportClose>" . __('Close') . "</a>";
            }
            print "<br>$message</p><div style=\"clear:both\"></div></div>";
        }

        /**
         * Пунтк меню с настройками
         */
        function pluginMenuSettings ()
        {
            add_options_page(__('Settings') . ' ' . $this->name, $this->name, 
                    'manage_options', $this->mnemo . '_settings', 
                    array(
                        $this,
                        'pluginPageSettings'));
        }

        /**
         * Страница с настройками
         */
        function pluginPageSettings ()
        {
            $text = $this->loadFile('settings-form.htm');
            $text = str_replace('{$title}', __('Settings') . ' ' . $this->name, 
                    $text);
            $text = str_replace('{$logo}', 
                    plugins_url('/teleport.png', __FILE__), $text);
            $text = str_replace('{$path}', 
                    $this->path('basedir') . $this->path($this->mnemo), $text);
            $text = str_replace('{$url}', get_site_url(), $text);
            $text = str_replace('{$stat}', $this->stat(), $text);
            $text = str_replace('{$feedback}', __('Feedback'), $text);

            $checked = '';
            $name = get_option('imegateleport-settings-fullname');
            if ($name == 'true')
                $checked = ' checked value=1';
            $text = str_replace('{$checked_name}', $checked, $text);

            $checked = '';
            $name = get_option('imegateleport-settings-kod');
            if ($name == 'true')
                $checked = ' checked value=1';
            $text = str_replace('{$checked_kod}', $checked, $text);

            $checked = '';
            $zip = get_option('imegateleport-settings-zip');
            if ($zip == 'true')
                $checked = ' checked value=1';
            $text = str_replace('{$checked_zip}', $checked, $text);
            
            echo $text;
        }

        /**
         * Отобразить прогресс или изменить значение
         *
         * @param int $value            
         * @return int
         */
        function progress ($value = null)
        {
            if (! isset($value)) {
                $value = get_option('imegateleport_progress');
                $this->log('    PROGRESS = ' . $value);
                if ($value == 100) {
                    delete_option('imegateleport_progress');
                    update_option('imegateleport_complete', 1);
                    $this->s('progress');
                }
            } else {
                update_option('imegateleport_progress', $value);
            }
            return $value;
        }

        function progressBarScripts ()
        {
            wp_enqueue_style('jquery-ui-style-css', 
                    'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css?ver=3.8');
            wp_enqueue_script('jquery-ui-progressbar');
            wp_register_script('iMegaTeleportProgressBar', 
                    plugins_url('/imegateleport.js', __FILE__));
            wp_enqueue_script('iMegaTeleportProgressBar');
        }

        /**
         * Маршруты
         *
         * @param bool $ajax            
         * @return bool
         */
        function routers ($ajax = false)
        {
            $result = false;
            if ($ajax && isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'imega_teleport':
                        $this->lazyQuery(self::LAZY_FILES);
                        $progress = $this->progress();
                        $error = get_option('imegateleport_error');
                        if ($error) {
                            $this->log('    ERROR = ' . $error);
                            $result['error'] = $error;
                        }
                        $result['complete'] = (int) get_option('imegateleport_complete', 0);
                        $result['progress'] = $progress;
                        echo json_encode($result);
                        $this->log("==PROGRESS = $progress");
                        exit();
                        break;
                    case 'imegateleport-settings':
                        echo $this->settings($_POST['param'], $_POST['value']);
                        exit();
                        break;
                }
            }
            /*
             * Проверка обращения клиента 1с
             */
            $agent = '';
            if (! $ajax) {
                $this->log('==GET==');
                $this->log($_GET);
                $this->log('==POST==');
                $this->log($_POST);
                $this->log('==SERVER==');
                $this->log($_SERVER);
            }
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $version = explode('.', phpversion());
                if ($version[1] >= 3) {
                    // PHP 5.3
                    $agent = strstr($_SERVER['HTTP_USER_AGENT'], '/', true);
                } else {
                    // PHP 5.2
                    $pos = strpos($_SERVER['HTTP_USER_AGENT'], '/');
                    $agent = substr($_SERVER['HTTP_USER_AGENT'], 0, $pos);
                }
            }
            if (! $ajax && $agent == '1C+Enterprise')
                if ($_GET['type'] == 'catalog')
                    return $this->transfer();

            if (! $ajax && $agent == '1C+Enterprise')
                if ($_GET['type'] == 'sale') {
                    $this->transfer();
                    return;
                }
                
            if (! $ajax && isset($_POST['action'])) {
                if ($_POST['action'] == 'imegagogo') {
                    $this->progress(5);
                    return true;
                }
            }
            if ($ajax && $agent == '1C+Enterprise' && $this->progress() > 0) {
                header("HTTP/1.0 404 Not Found");
                echo "\n\nСообщение от iMegaTeleport: Еще не завершен процесс обновления.\n";
                echo "== Что делать если индикатор процесса завис? ==\n1. Деактивируйте iMegaTeleport в панели управления блогом и снова активируйте.\n2. Выполните обмен с сайтом, но указав полную выгрузку товара.\n\n";
                exit();
            }
        }

        private function rmrf($dir) {
            if ($objs = glob($dir."/*")) {
                foreach($objs as $obj) {
                    is_dir($obj) ? $this->rmrf($obj) : unlink($obj);
                }
            }
            rmdir($dir);
        }

        /**
         * Запуск
         */
        function run($force = false)
        {
            if ($this->error && $this->force === $force) {
                return;
            }
            $this->sendQuery('set names ' . DB_CHARSET . ';');
            update_option('imegateleport_complete', 0);
            $this->log("==Mysql result==");
            $this->sendQuery($this->query);
            //$this->mysqli->close();
        }

        /**
         * Сбор запроса
         *
         * @return string
         */
        function runQuery ()
        {
            if ($this->error && $this->force === false) {
                return;
            }

            $query = $this->composer(self::COTHER,
                $this->loadFile($this->filenameTables),
                true);

            $query .= $this->loadImport();

            if ($this->sets['fullname'] == 'true') {
                $query = $this->composer(self::COTHER,
                    $this->loadFile($this->filenameFullname),
                    true);
            }

            $query .= $this->loadOffers();
            if (defined('IMEGATELEPORT_COMPOSER')) {
                foreach ($this->keys as $key => $value){
                    if (! empty($value)) {
                        $this->composer($key, '', true);
                    }
                }
            }

            $this->log('==BUSINESS LOGIC = '.$this->filenameBL);
            $queryBL = $this->loadFile($this->filenameBL);
            $queryBL = str_replace('{$baseurl}', $this->path('baseurl'), 
                    $queryBL);
            $queryBL = str_replace('{$imgpath}', $this->path($this->mnemo), 
                    $queryBL);
            $query .= $this->composer(self::COTHER, $queryBL, true);
            $queryBL = '';
            $this->progress(20);
            update_option('imegateleport_files', count($this->getComposer()));
            $this->query = $query;
        }

        private function sendQuery($value)
        {
            if (function_exists('mysqli_multi_query')) {
                $this->sendQueryMulti($value);
            } else {
                $this->sendQueryOne($value);
            }
        }

        private function sendQueryOne($value)
        {
            if (! empty($value) && ! $this->mysqli->query($value)) {
                $this->error = $this->mysqli->error;
                $lastQuery = get_option('imegateleport_query', 0);
                if ($lastQuery === 0) {
                    $lastQuery = mb_substr($value, 0, 199);
                }
                update_option('imegateleport_error', $this->mysqli->error . "<br>Query: $lastQuery...");
                return;
            }
        }

        private function sendQueryMulti($value)
        {
//            $lastQuery = get_option('imegateleport_query', 0);
            if (! $this->mysqli->multi_query($value)) {
                $this->error = $this->mysqli->connect_error;

                /*
                 * do {

                if ($result = $mysqli->store_result()) {
                    while ($row = $result->fetch_row()) {
                        printf("%s\n", $row[0]);
                    }
                    $result->free();
                }

                if ($mysqli->more_results()) {
                    printf("-----------------\n");
                }
            } while ($mysqli->next_result());
                */



                return;
            }
        }

        /**
         * Сохранение частей во временной директории
         * @param $data
         */
        private function saveComposer($type)
        {
            $data = $this->keys[$type];
            if (empty($this->values[$type])) {
                return;
            }
            foreach ($this->values[$type] as $value){
                $data .= $value;
            }
            if (empty($data)) {
                return;
            }
            $data = mb_substr($data, 0, -1) . ";";
            $path = $this->path('basedir') . $this->path($this->mnemo) . 'tmp/';
            $this->folder($path);
            $files = scandir($path, SCANDIR_SORT_DESCENDING);
            if (strpos($files[0], '.sql') >= 1){
                $fileName = (int) $files[0] + 1;
            } else {
                $fileName = 10000000;
            }
            $f = fopen($path . $fileName . '.sql', 'a');
            fwrite($f, $data);
            fclose($f);
        }
        /**
         * Временный файл
         */
        function temp ($value = '', $remove = false)
        {
            $contents = '';
            
            $filename = $this->path('basedir') . $this->path($this->mnemo) . 'imegatemp';
            
            if (! file_exists($filename) && empty($value)) {
                return $contents;
            }
            
            if (empty($value)) {
                $mode = 'r';
            } else {
                $mode = 'w';
            }
            
            if ($remove) {
                unlink($filename);
                return;
            }

            $f = fopen($filename, $mode);
            
            if (empty($value)) {
                $contents = fread($f, filesize($filename));
            } else {
                fwrite($f, $value);
            }
            
            fclose($f);
            return $contents;
        }

        /**
         * Unzip the zip-file in the destination dir
         *
         * @param string $zipFile            
         * @param string $destDir            
         */
        function unzip ($zipFile, &$listOfFiles, $destDir = false)
        {
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip) {
                    $open = $zip->open($zipFile);
                    if ($open === true) {
                        $zip->extractTo($destDir);
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            array_push($listOfFiles, $zip->statIndex($i));
                        }
                        $zip->close();
                    } else {
                        $this->error = '==ZIP ERROR: ' .
                                 $this->zipStatusString($open);
                    }
                    return true;
                }
            } else {
                return false;
            }
        }

        /**
         * Zip
         *
         * @param string $xml            
         */
        function xmlToZip ($xml)
        {
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                $path = $this->path('basedir') . $this->path($this->mnemo);
                $filename = 'order.zip';
                $res = $zip->open($path . $filename, ZipArchive::CREATE);
                if ($res === TRUE) {
                    $zip->addFromString('order.xml', $xml);
                    $zip->close();
                    header("Content-Type: application/zip");
                    header("Content-Disposition: attachment; filename=$filename");
                    header("Content-Length: " . filesize($path . $filename));
                    @readfile($path . $filename);
                    exit();
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
        function zipStatusString ($status)
        {
            switch ((int) $status) {
                case ZipArchive::ER_OK:
                    return 'N No error';
                case ZipArchive::ER_MULTIDISK:
                    return 'N Multi-disk zip archives not supported';
                case ZipArchive::ER_RENAME:
                    return 'S Renaming temporary file failed';
                case ZipArchive::ER_CLOSE:
                    return 'S Closing zip archive failed';
                case ZipArchive::ER_SEEK:
                    return 'S Seek error';
                case ZipArchive::ER_READ:
                    return 'S Read error';
                case ZipArchive::ER_WRITE:
                    return 'S Write error';
                case ZipArchive::ER_CRC:
                    return 'N CRC error';
                case ZipArchive::ER_ZIPCLOSED:
                    return 'N Containing zip archive was closed';
                case ZipArchive::ER_NOENT:
                    return 'N No such file';
                case ZipArchive::ER_EXISTS:
                    return 'N File already exists';
                case ZipArchive::ER_OPEN:
                    return 'S Can\'t open file';
                case ZipArchive::ER_TMPOPEN:
                    return 'S Failure to create temporary file';
                case ZipArchive::ER_ZLIB:
                    return 'Z Zlib error';
                case ZipArchive::ER_MEMORY:
                    return 'N Malloc failure';
                case ZipArchive::ER_CHANGED:
                    return 'N Entry has been changed';
                case ZipArchive::ER_COMPNOTSUPP:
                    return 'N Compression method not supported';
                case ZipArchive::ER_EOF:
                    return 'N Premature EOF';
                case ZipArchive::ER_INVAL:
                    return 'N Invalid argument';
                case ZipArchive::ER_NOZIP:
                    return 'N Not a zip archive';
                case ZipArchive::ER_INTERNAL:
                    return 'N Internal error';
                case ZipArchive::ER_INCONS:
                    return 'N Zip archive inconsistent';
                case ZipArchive::ER_REMOVE:
                    return 'S Can\'t remove file';
                case ZipArchive::ER_DELETED:
                    return 'N Entry has been deleted';
                
                default:
                    return sprintf('Unknown status %s', $status);
            }
        }
    }
    
    $GLOBALS['iMegaTeleport'] = new iMegaTeleport();
}
