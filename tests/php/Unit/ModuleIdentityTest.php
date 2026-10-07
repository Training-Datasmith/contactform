<?php

class ModuleIdentityTest extends ContactformTestCase
{
    public function testModuleMetadataMatchesConfigXml()
    {
        $xml = simplexml_load_file(dirname(__DIR__, 3) . '/config.xml');
        $this->assertSame((string) $xml->name, $this->module->name);
        $this->assertSame((string) $xml->version, $this->module->version);
        $this->assertSame((string) $xml->author, $this->module->author);
    }
}
