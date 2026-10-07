<?php

class UserAgentVersion902Test extends ContactformTestCase
{
    public function testUserAgentIsTruncatedTo255CharactersOn902()
    {
        if (_PS_VERSION_ !== '9.0.2') {
            $this->markTestSkipped('Run this file with PS_VERSION_UNDER_TEST=9.0.2');
        }

        $_SERVER['HTTP_USER_AGENT'] = str_repeat('B', 400);
        $this->seedValidSubmit();
        $this->module->sendMessage();

        $this->assertCount(1, CustomerMessageRepository::$messages);
        $this->assertSame(255, strlen(CustomerMessageRepository::$messages[0]['user_agent']));
    }
}
