<?php

class GetTemplateVarContactTest extends ContactformTestCase
{
    public function testUnknownThreadContactFallsBackToAllContacts()
    {
        $reflection = new ReflectionClass($this->module);
        $property = $reflection->getProperty('customer_thread');
        $property->setAccessible(true);
        $property->setValue($this->module, ['id_contact' => 999]);

        $contacts = $this->module->getTemplateVarContact();
        $this->assertCount(2, $contacts);
        $this->assertArrayHasKey(1, $contacts);
        $this->assertArrayHasKey(2, $contacts);
    }

    public function testKnownThreadContactReturnsSingleEntry()
    {
        $reflection = new ReflectionClass($this->module);
        $property = $reflection->getProperty('customer_thread');
        $property->setAccessible(true);
        $property->setValue($this->module, ['id_contact' => 2]);

        $contacts = $this->module->getTemplateVarContact();
        $this->assertCount(1, $contacts);
        $this->assertSame(2, $contacts[0]['id_contact']);
    }
}
