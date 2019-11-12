<?php

namespace jerkob\Salesforce\Exception;

use GuzzleHttp\Exception\ClientException;

class SalesforceException extends \Exception
{
    private static $errorMessage = "Salesforce request error";

    /** @var array */
    private $errors = [];

    public static function fromClientException(ClientException $e)
    {
        $responseString = $e->getResponse()->getBody()->getContents();
        $responseData = json_decode($responseString, true);
        $ret = new self(self::$errorMessage, $e->getResponse()->getStatusCode());
        $ret->setErrors($responseData);
        return $ret;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;
    }
}
