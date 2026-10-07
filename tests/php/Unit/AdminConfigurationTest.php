<?php

class AdminConfigurationTest extends ContactformTestCase
{
    public function testSavingConfigurationUpdatesBothEmailSwitches()
    {
        Tools::setValue(Contactform::SUBMIT_NAME, '1');
        Tools::setValue(Contactform::SEND_CONFIRMATION_EMAIL, '1');
        Tools::setValue(Contactform::SEND_NOTIFICATION_EMAIL, '0');

        try {
            $this->module->getContent();
            $this->fail('Expected redirect');
        } catch (RuntimeException $e) {
            $this->assertContains('redirect:', $e->getMessage());
        }

        $this->assertSame('1', Configuration::get(Contactform::SEND_CONFIRMATION_EMAIL));
        $this->assertSame('0', Configuration::get(Contactform::SEND_NOTIFICATION_EMAIL));
        $this->assertSame('CONTACTFORM_SEND_CONFIRMATION_EMAIL', Contactform::SEND_CONFIRMATION_EMAIL);
        $this->assertSame('CONTACTFORM_SEND_NOTIFICATION_EMAIL', Contactform::SEND_NOTIFICATION_EMAIL);
    }
}
