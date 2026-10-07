<?php

class GetWidgetVariablesTest extends ContactformTestCase
{
    public function testExpiredTokenIsRefreshedBeforeRender()
    {
        $this->context->cookie->contactFormToken = 'old';
        $this->context->cookie->contactFormTokenTTL = time() - 10;

        $vars = $this->module->getWidgetVariables();
        $this->assertNotSame('old', $vars['token']);
        $this->assertGreaterThanOrEqual(time(), $this->context->cookie->contactFormTokenTTL);
        $this->assertLessThanOrEqual(time() + 600, $this->context->cookie->contactFormTokenTTL);
    }

    public function testLooseEqualThreadTokenDoesNotExposeThreadEmail()
    {
        CustomerThreadRepository::$threads[3] = [
            'id' => 3,
            'email' => 'thread@example.com',
            'token' => '1e0',
            'id_contact' => 1,
        ];
        Tools::setValue('id_customer_thread', 3);
        Tools::setValue('token', '1');

        $vars = $this->module->getWidgetVariables();
        $this->assertNotSame('thread@example.com', $vars['contact']['email']);
    }

    public function testNonStringThreadTokenDoesNotExposeThread()
    {
        CustomerThreadRepository::$threads[3] = [
            'id' => 3,
            'email' => 'thread@example.com',
            'token' => 'secret',
            'id_contact' => 1,
        ];
        Tools::setValue('id_customer_thread', 3);
        Tools::setValue('token', ['not-a-string']);

        $vars = $this->module->getWidgetVariables();
        $this->assertNotSame('thread@example.com', $vars['contact']['email']);
    }

    public function testValidStringThreadTokenExposesThreadEmail()
    {
        CustomerThreadRepository::$threads[3] = [
            'id' => 3,
            'email' => 'thread@example.com',
            'token' => 'secret',
            'id_contact' => 1,
        ];
        Tools::setValue('id_customer_thread', 3);
        Tools::setValue('token', 'secret');

        $vars = $this->module->getWidgetVariables();
        $this->assertSame('thread@example.com', $vars['contact']['email']);
    }
}
