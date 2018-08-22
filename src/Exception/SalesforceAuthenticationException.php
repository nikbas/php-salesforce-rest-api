<?php

namespace bjsmasth\Salesforce\Exception;

use GuzzleHttp\Exception\ClientException;

class SalesforceAuthenticationException extends SalesforceException
{
    private static $errorMessage = "Salesforce authentication request error";

    public static function fromClientException(ClientException $e)
    {
        $responseString = $e->getResponse()->getBody()->getContents();
        $responseData = json_decode($responseString, true);
        $ret = new self(self::$errorMessage, $e->getResponse()->getStatusCode());
        $ret->setErrors($responseData);
        return $ret;
    }
}
