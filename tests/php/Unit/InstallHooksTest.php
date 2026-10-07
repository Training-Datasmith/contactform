<?php

class InstallHooksTest extends ContactformTestCase
{
    public function testRegisteredHooksAreHookableOn()
    {
        $this->assertTrue($this->module->install());
        $this->assertTrue($this->module->isHookableOn('registerGDPRConsent'));
        $this->assertTrue($this->module->isHookableOn('displayContactContent'));
        $this->assertFalse($this->module->isHookableOn('displayHeader'));
    }
}
