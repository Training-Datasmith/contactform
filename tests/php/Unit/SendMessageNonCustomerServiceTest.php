<?php

class SendMessageNonCustomerServiceTest extends ContactformTestCase
{
    public function testNonCustomerServiceWithBothMailsDisabledFails()
    {
        Configuration::reset([
            Contactform::SEND_CONFIRMATION_EMAIL => 0,
            Contactform::SEND_NOTIFICATION_EMAIL => 0,
        ]);
        $this->seedValidSubmit(['id_contact' => 2]);
        $this->module->sendMessage();

        $this->assertCount(1, $this->context->controller->errors);
        $this->assertSame(0, count(Mail::$calls));
    }

    public function testNonCustomerServiceWithConfirmationOnlySucceeds()
    {
        Configuration::reset([
            Contactform::SEND_CONFIRMATION_EMAIL => 1,
            Contactform::SEND_NOTIFICATION_EMAIL => 0,
        ]);
        $this->seedValidSubmit(['id_contact' => 2]);
        $this->module->sendMessage();

        $this->assertEmpty($this->context->controller->errors);
        $this->assertCount(1, Mail::$calls);
        $this->assertSame('contact_form', Mail::$calls[0]['template']);
    }

    public function testNonCustomerServiceWithNotificationOnlySucceeds()
    {
        Configuration::reset([
            Contactform::SEND_CONFIRMATION_EMAIL => 0,
            Contactform::SEND_NOTIFICATION_EMAIL => 1,
        ]);
        $this->seedValidSubmit(['id_contact' => 2]);
        $this->module->sendMessage();

        $this->assertEmpty($this->context->controller->errors);
        $this->assertCount(1, Mail::$calls);
        $this->assertSame('contact', Mail::$calls[0]['template']);
    }
}
