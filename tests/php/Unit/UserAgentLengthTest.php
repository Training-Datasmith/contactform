<?php

class UserAgentLengthTest extends ContactformTestCase
{
    public function testUserAgentIsTruncatedTo128CharactersBefore902()
    {
        $_SERVER['HTTP_USER_AGENT'] = str_repeat('A', 300);
        $this->seedValidSubmit();
        $this->module->sendMessage();

        $this->assertCount(1, CustomerMessageRepository::$messages);
        $this->assertSame(128, strlen(CustomerMessageRepository::$messages[0]['user_agent']));
    }
}
