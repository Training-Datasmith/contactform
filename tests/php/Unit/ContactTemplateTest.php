<?php

class ContactTemplateTest extends ContactformTestCase
{
    public function testTemplateRendersExpectedFieldsAndHoneypot()
    {
        $html = $this->renderTemplate([
            'contact' => [
                'contacts' => [
                    1 => ['id_contact' => 1, 'name' => 'Customer service'],
                ],
                'email' => 'a@b.com',
                'message' => 'saved',
                'allow_file_upload' => true,
                'orders' => [
                    7 => ['id_order' => 7, 'reference' => 'REF7'],
                ],
            ],
            'notifications' => null,
            'token' => 'csrf-token',
            'id_module' => 42,
        ]);

        $this->assertContains('type="hidden"', $html);
        $this->assertContains('name="id_contact"', $html);
        $this->assertContains('name="from"', $html);
        $this->assertContains('name="message"', $html);
        $this->assertContains('name="token"', $html);
        $this->assertContains('name="submitMessage"', $html);
        $this->assertContains('name="fileUpload"', $html);
        $this->assertContains('name="id_order"', $html);
        $this->assertContains('name="url"', $html);
        $this->assertContains('enctype="multipart/form-data"', $html);
        $this->assertContains('csrf-token', $html);
        $this->assertContains('saved', $html);
    }

    public function testMultiContactRendersSelectAndOmitsEnctypeWhenUploadsDisabled()
    {
        $html = $this->renderTemplate([
            'contact' => [
                'contacts' => [
                    1 => ['id_contact' => 1, 'name' => 'Customer service'],
                    2 => ['id_contact' => 2, 'name' => 'Sales'],
                ],
                'email' => '',
                'message' => '',
                'allow_file_upload' => false,
                'orders' => [],
            ],
            'notifications' => [
                'messages' => ['Oops'],
                'nw_error' => true,
            ],
            'token' => 't',
            'id_module' => 1,
        ]);

        $this->assertContains('<select name="id_contact">', $html);
        $this->assertNotContains('enctype="multipart/form-data"', $html);
        $this->assertContains('notification-error', $html);
        $this->assertContains('Oops', $html);
    }

    public function testSuccessNotificationHidesFormFields()
    {
        $html = $this->renderTemplate([
            'contact' => [
                'contacts' => [1 => ['id_contact' => 1, 'name' => 'CS']],
                'email' => '',
                'message' => '',
                'allow_file_upload' => false,
                'orders' => [],
            ],
            'notifications' => [
                'messages' => ['Sent'],
                'nw_error' => false,
            ],
            'token' => 't',
            'id_module' => 1,
        ]);

        $this->assertContains('notification-success', $html);
        $this->assertNotContains('name="submitMessage"', $html);
    }

    private function renderTemplate(array $vars)
    {
        $template = dirname(__DIR__, 3) . '/views/templates/widget/contactform.tpl';
        $smarty = new Smarty();
        $smarty->registerPlugin('function', 'l', function ($params) {
            return isset($params['s']) ? $params['s'] : '';
        });
        $smarty->registerPlugin('function', 'hook', function () {
            return '';
        });
        $smarty->setCompileDir(sys_get_temp_dir() . '/smarty-compile-contact');
        $smarty->setCacheDir(sys_get_temp_dir() . '/smarty-cache-contact');
        if (!is_dir($smarty->getCompileDir())) {
            mkdir($smarty->getCompileDir(), 0777, true);
        }
        if (!is_dir($smarty->getCacheDir())) {
            mkdir($smarty->getCacheDir(), 0777, true);
        }
        $smarty->assign('urls', ['pages' => ['contact' => '/contact']]);
        foreach ($vars as $key => $value) {
            $smarty->assign($key, $value);
        }

        return $smarty->fetch($template);
    }
}
