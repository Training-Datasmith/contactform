<?php

class SendMessageMailTest extends ContactformTestCase
{
    public function testNotificationMailUsesLiteralTemplateVariables()
    {
        Configuration::reset([
            Contactform::SEND_CONFIRMATION_EMAIL => 0,
            Contactform::SEND_NOTIFICATION_EMAIL => 1,
        ]);
        $message = "Line one\nLine two";
        $this->seedValidSubmit(['message' => $message]);
        $this->module->sendMessage();

        $this->assertCount(1, Mail::$calls);
        $call = Mail::$calls[0];
        $this->assertSame('contact', $call['template']);
        $this->assertSame('service@example.com', $call['to']);
        $this->assertSame($this->prestashopNl2brMessage(stripslashes($message)), $call['var_list']['{message}']);
        $this->assertSame('customer@example.com', $call['var_list']['{email}']);
    }

    public function testConfirmationMailDoesNotAttachCustomerFile()
    {
        Configuration::reset([
            'PS_CUSTOMER_SERVICE_FILE_UPLOAD' => 1,
            Contactform::SEND_CONFIRMATION_EMAIL => 1,
            Contactform::SEND_NOTIFICATION_EMAIL => 0,
        ]);
        $_FILES['fileUpload'] = [
            'name' => 'proof.pdf',
            'tmp_name' => sys_get_temp_dir() . '/proof.pdf',
            'error' => 0,
            'rename' => 'proof.pdf',
        ];
        if (!is_dir(_PS_UPLOAD_DIR_)) {
            mkdir(_PS_UPLOAD_DIR_, 0777, true);
        }
        file_put_contents($_FILES['fileUpload']['tmp_name'], 'pdf');
        Tools::$fileAttachmentCallback = function ($input) {
            return $_FILES[$input];
        };

        $this->seedValidSubmit();
        $this->module->sendMessage();

        $confirmation = null;
        foreach (Mail::$calls as $call) {
            if ($call['template'] === 'contact_form') {
                $confirmation = $call;
            }
        }
        $this->assertNotNull($confirmation);
        $this->assertNull($confirmation['file_attachment']);
        $this->assertSame(Contactform::MESSAGE_PLACEHOLDER_FOR_OLDER_VERSION, $confirmation['var_list']['{message}']);
    }
}
