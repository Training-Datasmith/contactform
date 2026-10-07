<?php

class SendMessageStripslashesPre8Test extends ContactformTestCase
{
    public function testNotificationMailStripslashesMessageOnPre80PrestaShop()
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            $this->markTestSkipped('Run this file with PS_VERSION_UNDER_TEST below 8.0.0');
        }

        Configuration::reset([
            Contactform::SEND_CONFIRMATION_EMAIL => 0,
            Contactform::SEND_NOTIFICATION_EMAIL => 1,
        ]);
        $this->seedValidSubmit(['message' => 'slash\\test']);
        $this->module->sendMessage();

        $this->assertCount(1, Mail::$calls);
        $this->assertSame('slashtest', Mail::$calls[0]['var_list']['{message}']);
    }
}
