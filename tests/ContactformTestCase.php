<?php

abstract class ContactformTestCase extends PHPUnit_Framework_TestCase
{
    /** @var Contactform */
    protected $module;

    /** @var Context */
    protected $context;

    protected function setUp()
    {
        parent::setUp();
        Tools::reset();
        Configuration::reset();
        Mail::reset();
        CustomerThreadRepository::reset();
        CustomerMessageRepository::reset();
        OrderRepository::reset();
        Context::$instance = null;
        $this->context = Context::getContext();
        $this->module = new Contactform();
        $this->module->context = $this->context;
        $_SERVER['HTTP_USER_AGENT'] = str_repeat('U', 200);
    }

    protected function seedValidSubmit(array $overrides = [])
    {
        $token = 'valid-token';
        $this->context->cookie->contactFormToken = $token;
        $this->context->cookie->contactFormTokenTTL = time() + 600;
        Tools::setValue('token', $token);
        Tools::setValue('from', 'customer@example.com');
        Tools::setValue('message', 'Hello support');
        Tools::setValue('id_contact', 1);
        Tools::setValue('url', '');
        $_POST['submitMessage'] = '1';
        foreach ($overrides as $key => $value) {
            Tools::setValue($key, $value);
        }
    }

    protected function prestashopNl2brMessage($message)
    {
        return Tools::nl2br(Tools::htmlentitiesUTF8($message));
    }
}
