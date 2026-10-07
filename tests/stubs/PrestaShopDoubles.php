<?php

namespace PrestaShop\PrestaShop\Core\Module {
    interface WidgetInterface
    {
        public function renderWidget($hookName = null, array $configuration = []);

        public function getWidgetVariables($hookName = null, array $configuration = []);
    }
}

namespace {
    if (!defined('_PS_VERSION_')) {
        $psVersion = getenv('PS_VERSION_UNDER_TEST') ?: '8.1.7';
        define('_PS_VERSION_', $psVersion);
    }
    if (!defined('_PS_ROOT_DIR_')) {
        define('_PS_ROOT_DIR_', dirname(__DIR__, 2));
    }
    if (!defined('_PS_MODULE_DIR_')) {
        define('_PS_MODULE_DIR_', _PS_ROOT_DIR_ . '/');
    }
    if (!defined('_PS_UPLOAD_DIR_')) {
        define('_PS_UPLOAD_DIR_', sys_get_temp_dir() . '/ps-upload/');
    }
    if (!defined('_PS_MAIL_DIR_')) {
        define('_PS_MAIL_DIR_', _PS_ROOT_DIR_ . '/mails/');
    }

    class Module
    {
        public $name;
        public $author;
        public $tab;
        public $version;
        public $bootstrap;
        public $displayName;
        public $description;
        public $ps_versions_compliancy;
        public $active = true;
        public $id = 42;
        public $table = 'module';
        public $smarty;
        protected $hooks = [];

        public function __construct()
        {
            $this->smarty = new SmartyStub();
        }

        public function trans($id, array $parameters = [], $domain = null, $locale = null)
        {
            return $id;
        }

        public function install()
        {
            return true;
        }

        public function registerHook($hooks)
        {
            if (!is_array($hooks)) {
                $hooks = [$hooks];
            }
            foreach ($hooks as $hook) {
                $this->hooks[] = $hook;
            }

            return true;
        }

        public function isHookableOn($hookName)
        {
            $needle = strtolower($hookName);
            foreach ($this->hooks as $hook) {
                if (strtolower($hook) === $needle) {
                    return true;
                }
            }

            return false;
        }

        public function display($file, $template)
        {
            return $this->smarty->fetch(_PS_ROOT_DIR_ . '/' . $template);
        }

        protected function l($string, $specific = false, $locale = null)
        {
            return $string;
        }
    }

    class Context
    {
        /** @var Context */
        public static $instance;

        /** @var Cookie */
        public $cookie;

        /** @var Customer */
        public $customer;

        /** @var Shop */
        public $shop;

        /** @var Language */
        public $language;

        /** @var FrontController */
        public $controller;

        /** @var Link */
        public $link;

        public function __construct()
        {
            $this->cookie = new Cookie();
            $this->customer = new Customer();
            $this->shop = new Shop();
            $this->language = new Language(1);
            $this->controller = new FrontController();
            $this->link = new Link();
            self::$instance = $this;
        }

        public static function getContext()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }
    }

    class Cookie
    {
        public $contactFormToken;
        public $contactFormTokenTTL;
        public $email;
    }

    class Shop
    {
        public $id = 1;
    }

    class Language
    {
        public $id;

        public function __construct($id)
        {
            $this->id = (int) $id;
        }
    }

    class FrontController
    {
        public $errors = [];
        public $success = [];
        public $objectPresenter;

        public function __construct()
        {
            $this->objectPresenter = new ObjectPresenter();
        }

        public function getLanguages()
        {
            return [new Language(1)];
        }
    }

    class ObjectPresenter
    {
        public function present($object)
        {
            if ($object instanceof CustomerThread) {
                return [
                    'id_customer_thread' => $object->id,
                    'id_contact' => $object->id_contact,
                    'id_order' => $object->id_order,
                    'id_product' => $object->id_product,
                    'email' => $object->email,
                    'token' => $object->token,
                ];
            }
            if ($object instanceof Order) {
                return [
                    'id_order' => $object->id,
                    'reference' => $object->reference,
                ];
            }
            if ($object instanceof Product) {
                return [
                    'id_product' => $object->id,
                    'name' => $object->name,
                ];
            }

            return [];
        }
    }

    class Link
    {
        public function getAdminLink($controller, $withToken = true)
        {
            return 'http://localhost/admin/index.php?controller=' . $controller;
        }
    }

    class Configuration
    {
        private static $values = [
            'PS_LANG_DEFAULT' => 1,
            'PS_CUSTOMER_SERVICE_FILE_UPLOAD' => 0,
            Contactform::SEND_CONFIRMATION_EMAIL => 0,
            Contactform::SEND_NOTIFICATION_EMAIL => 1,
        ];

        public static function reset(array $values = [])
        {
            self::$values = array_merge([
                'PS_LANG_DEFAULT' => 1,
                'PS_CUSTOMER_SERVICE_FILE_UPLOAD' => 0,
                Contactform::SEND_CONFIRMATION_EMAIL => 0,
                Contactform::SEND_NOTIFICATION_EMAIL => 1,
            ], $values);
        }

        public static function get($key)
        {
            return isset(self::$values[$key]) ? self::$values[$key] : null;
        }

        public static function updateValue($key, $value)
        {
            self::$values[$key] = $value;

            return true;
        }

        public static function isCatalogMode()
        {
            return false;
        }
    }

    class Validate
    {
        public static function isEmail($email)
        {
            return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
        }

        public static function isCleanHtml($message)
        {
            return true;
        }

        public static function isLoadedObject($object)
        {
            return is_object($object) && !empty($object->id);
        }
    }

    class Tools
    {
        private static $values = [];
        public static $fileAttachmentCallback;
        public static $lastFileAttachmentInput;

        public static function reset()
        {
            self::$values = [];
            self::$fileAttachmentCallback = null;
            self::$lastFileAttachmentInput = null;
            $_GET = [];
            $_POST = [];
            $_FILES = [];
            $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit-UA';
        }

        public static function setValue($key, $value)
        {
            self::$values[$key] = $value;
            $_POST[$key] = $value;
        }

        public static function getValue($key, $default = false)
        {
            if (isset(self::$values[$key])) {
                return self::$values[$key];
            }
            if (isset($_POST[$key])) {
                return $_POST[$key];
            }
            if (isset($_GET[$key])) {
                return $_GET[$key];
            }

            return $default;
        }

        public static function isSubmit($key)
        {
            return isset($_POST[$key]) || isset(self::$values[$key]);
        }

        public static function getAdminTokenLite($tab)
        {
            return 'token';
        }

        public static function redirectAdmin($url)
        {
            throw new RuntimeException('redirect:' . $url);
        }

        public static function safeOutput($value)
        {
            return $value;
        }

        public static function fileAttachment($input)
        {
            self::$lastFileAttachmentInput = $input;
            if (self::$fileAttachmentCallback) {
                return call_user_func(self::$fileAttachmentCallback, $input);
            }
            if (empty($_FILES[$input]['name']) || empty($_FILES[$input]['tmp_name'])) {
                return null;
            }

            return $_FILES[$input];
        }

        public static function strtolower($string)
        {
            return strtolower($string);
        }

        public static function substr($string, $start, $length = null)
        {
            return null === $length ? substr($string, $start) : substr($string, $start, $length);
        }

        public static function nl2br($string)
        {
            return str_replace(["\r\n", "\r", "\n"], '<br />', $string);
        }

        public static function htmlentitiesUTF8($string)
        {
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }

        public static function getRemoteAddr()
        {
            return '127.0.0.1';
        }

        public static function passwdGen($length)
        {
            return str_repeat('a', (int) $length);
        }
    }

    class Contact
    {
        public $id;
        public $email = 'shop@example.com';
        public $name = 'Shop';
        public $customer_service = true;

        public function __construct($id = null, $idLang = null)
        {
            $this->id = (int) $id;
            if ($this->id === 1) {
                $this->customer_service = true;
                $this->email = 'service@example.com';
                $this->name = 'Customer service';
            } elseif ($this->id === 2) {
                $this->customer_service = false;
                $this->email = 'sales@example.com';
                $this->name = 'Sales';
            } elseif ($this->id > 0) {
                $this->customer_service = true;
            }
        }

        public static function getContacts($idLang)
        {
            return [
                ['id_contact' => 1, 'name' => 'Customer service', 'email' => 'service@example.com'],
                ['id_contact' => 2, 'name' => 'Sales', 'email' => 'sales@example.com'],
            ];
        }
    }

    class Customer
    {
        public $id = 0;
        public $firstname = 'John';
        public $lastname = 'Doe';

        public function isLogged()
        {
            return $this->id > 0;
        }

        public function getByEmail($email)
        {
            return false;
        }
    }

    class Order
    {
        public $id;
        public $id_customer;
        public $reference = 'XKBKNABJK';

        public function __construct($id = null)
        {
            $this->id = (int) $id;
            $this->id_customer = OrderRepository::$orders[$this->id]['id_customer'] ?? 0;
        }

        public function getUniqReference()
        {
            return $this->reference;
        }

        public function getProducts()
        {
            return OrderRepository::$orders[$this->id]['products'] ?? [];
        }
    }

    class OrderRepository
    {
        public static $orders = [];

        public static function reset()
        {
            self::$orders = [];
        }
    }

    class Product
    {
        public $id;
        public $name = [1 => 'Test product'];

        public function __construct($id = null)
        {
            $this->id = (int) $id;
        }
    }

    class CustomerThread
    {
        public $id;
        public $status;
        public $id_lang;
        public $id_contact;
        public $id_order;
        public $id_product;
        public $id_customer;
        public $id_shop;
        public $email;
        public $token = 'thread-token';

        public function __construct($id = null)
        {
            $this->id = (int) $id;
            if ($id && isset(CustomerThreadRepository::$threads[$id])) {
                foreach (CustomerThreadRepository::$threads[$id] as $key => $value) {
                    $this->$key = $value;
                }
            }
        }

        public function update()
        {
            CustomerThreadRepository::$threads[$this->id] = get_object_vars($this);

            return true;
        }

        public function add()
        {
            $this->id = ++CustomerThreadRepository::$autoId;
            CustomerThreadRepository::$threads[$this->id] = get_object_vars($this);

            return true;
        }

        public static function getIdCustomerThreadByEmailAndIdOrder($email, $idOrder)
        {
            return CustomerThreadRepository::getIdCustomerThreadByEmailAndIdOrder($email, $idOrder);
        }
    }

    class CustomerThreadRepository
    {
        public static $threads = [];
        public static $autoId = 0;

        public static function reset()
        {
            self::$threads = [];
            self::$autoId = 0;
        }

        public static function getIdCustomerThreadByEmailAndIdOrder($email, $idOrder)
        {
            foreach (self::$threads as $id => $thread) {
                if ($thread['email'] === $email && (int) $thread['id_order'] === (int) $idOrder) {
                    return $id;
                }
            }

            return 0;
        }
    }

    class CustomerMessage
    {
        public $id_customer_thread;
        public $message;
        public $file_name;
        public $ip_address;
        public $user_agent;

        public function add()
        {
            CustomerMessageRepository::$messages[] = get_object_vars($this);

            return true;
        }

        public static function getLastMessageForCustomerThread($idThread)
        {
            return CustomerMessageRepository::getLastMessage($idThread);
        }
    }

    class CustomerMessageRepository
    {
        public static $messages = [];

        public static function reset()
        {
            self::$messages = [];
        }

        public static function getLastMessage($idThread)
        {
            $last = '';
            foreach (self::$messages as $message) {
                if ((int) $message['id_customer_thread'] === (int) $idThread) {
                    $last = $message['message'];
                }
            }

            return $last;
        }
    }

    class Mail
    {
        public static $calls = [];

        public static function reset()
        {
            self::$calls = [];
        }

        public static function Send(
            $idLang,
            $template,
            $subject,
            $varList,
            $to,
            $toName = null,
            $from = null,
            $fromName = null,
            $fileAttachment = null,
            $mode_smtp = null,
            $templatePath = null,
            $die = false,
            $idShop = null,
            $bcc = null,
            $replyTo = null
        ) {
            self::$calls[] = [
                'template' => $template,
                'subject' => $subject,
                'var_list' => $varList,
                'to' => $to,
                'file_attachment' => $fileAttachment,
                'reply_to' => $replyTo,
            ];

            return true;
        }
    }

    class HelperForm
    {
        public $table;
        public $default_form_language;
        public $submit_action;
        public $currentIndex;
        public $token;
        public $tpl_vars = [];

        public function generateForm(array $forms)
        {
            return '<form>' . json_encode($forms) . '</form>';
        }
    }

    class SmartyStub
    {
        public $templateVars = [];

        public function assign($tplVar, $value = null)
        {
            if (is_array($tplVar)) {
                $this->templateVars = array_merge($this->templateVars, $tplVar);
            } else {
                $this->templateVars[$tplVar] = $value;
            }
        }

        public function fetch($templatePath)
        {
            return SmartyTemplateRenderer::render($templatePath, $this->templateVars);
        }
    }

    class SmartyTemplateRenderer
    {
        public static function render($templatePath, array $vars)
        {
            $smarty = new Smarty();
            $smarty->setCompileDir(sys_get_temp_dir() . '/smarty-compile');
            $smarty->setCacheDir(sys_get_temp_dir() . '/smarty-cache');
            if (!is_dir($smarty->getCompileDir())) {
                mkdir($smarty->getCompileDir(), 0777, true);
            }
            if (!is_dir($smarty->getCacheDir())) {
                mkdir($smarty->getCacheDir(), 0777, true);
            }
            foreach ($vars as $key => $value) {
                $smarty->assign($key, $value);
            }

            return $smarty->fetch($templatePath);
        }
    }
}
