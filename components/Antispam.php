<?php

namespace app\components;

use Cleantalk\CleantalkAntispam;

class Antispam
{
    const API_KEY = 'mahyzaje4enunav';

    private $apiKey;
    private $email_field;
    private $user_name_field;
    private $message_field;
    private $type_form = 'contact';

    public function __construct($formFields = array())
    {
        $this->apiKey = self::API_KEY;
        $this->email_field = 'email';
        $this->user_name_field = 'name';
        $this->message_field = 'job_title';
    }

    public function checkForm()
    {
        $cleantalk_antispam = new CleantalkAntispam($this->apiKey, $this->email_field, $this->user_name_field, $this->message_field, $this->type_form);

        $api_result = $cleantalk_antispam->handle();

        return $api_result;
    }
}
