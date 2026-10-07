<?php

class InstallHooksTest extends ContactformTestCase
{
    public function testInstallRegistersHooksThatAreHookableOn()
    {
        $this->assertTrue($this->module->install());

        $registeredHooks = ['registerGDPRConsent', 'displayContactContent'];
        foreach ($registeredHooks as $hookName) {
            $this->assertTrue(
                $this->module->isHookableOn($hookName),
                'Expected isHookableOn to accept hook registered by install(): ' . $hookName
            );
        }

        $this->assertFalse($this->module->isHookableOn('displayHeader'));
    }

    public function testRegisterGdprConsentHookRequiresListenerMethod()
    {
        $module = new Contactform();
        $this->assertTrue(method_exists($module, 'hookRegisterGDPRConsent'));
        $this->assertTrue(is_callable([$module, 'hookRegisterGDPRConsent']));
    }
}
