<?php

class SendMessageUploadTest extends ContactformTestCase
{
    public function testFileAttachmentIsSkippedWhenUploadsDisabled()
    {
        Configuration::reset(['PS_CUSTOMER_SERVICE_FILE_UPLOAD' => 0]);
        $_FILES['fileUpload'] = [
            'name' => 'note.pdf',
            'tmp_name' => '/tmp/note.pdf',
            'error' => 0,
        ];
        $called = false;
        Tools::$fileAttachmentCallback = function () use (&$called) {
            $called = true;

            return null;
        };

        $this->seedValidSubmit();
        $this->module->sendMessage();

        $this->assertEmpty($this->context->controller->errors);
        $this->assertFalse($called);
        $this->assertNull(Tools::$lastFileAttachmentInput);
    }

    public function testFailedUploadReportsErrorWhenNamePosted()
    {
        Configuration::reset(['PS_CUSTOMER_SERVICE_FILE_UPLOAD' => 1]);
        $_FILES['fileUpload'] = ['name' => 'broken.pdf'];
        Tools::$fileAttachmentCallback = function () {
            return null;
        };

        $this->seedValidSubmit();
        $this->module->sendMessage();

        $this->assertCount(1, $this->context->controller->errors);
        $this->assertSame(
            'An error occurred during the file-upload process.',
            $this->context->controller->errors[0]
        );
    }
}
