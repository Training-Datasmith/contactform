<?php

class SendMessageDuplicateTest extends ContactformTestCase
{
    public function testStrictDuplicateCheckTreatsZeroAndEmptyStringAsDifferent()
    {
        Configuration::reset([
            Contactform::SEND_CONFIRMATION_EMAIL => 0,
            Contactform::SEND_NOTIFICATION_EMAIL => 0,
        ]);
        CustomerThreadRepository::$threads[1] = [
            'id' => 1,
            'email' => 'customer@example.com',
            'id_order' => 0,
            'id_contact' => 1,
            'token' => 'abc',
        ];
        CustomerMessageRepository::$messages[] = [
            'id_customer_thread' => 1,
            'message' => 0,
        ];

        $this->seedValidSubmit(['message' => '0e0']);

        $before = count(CustomerMessageRepository::$messages);
        $this->module->sendMessage();
        $after = count(CustomerMessageRepository::$messages);

        $this->assertSame($before + 1, $after);
    }
}
