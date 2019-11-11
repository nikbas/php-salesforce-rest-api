<?php

namespace jerkob\Salesforce\Authentication;

use jerkob\Salesforce\Exception\SalesforceAuthenticationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class PasswordAuthentication implements AuthenticationInterface
{
    protected $client;
    protected $endPoint;
    protected $options;
    protected $access_token;
    protected $instance_url;

    public function __construct(array $options)
    {
        $this->endPoint = 'https://login.salesforce.com/';
        $this->options = $options;
    }

    public function authenticate()
    {
        $client = new Client();

        try {
            $request = $client->request(
                "post",
                "{$this->endPoint}services/oauth2/token",
                ['form_params' => $this->options]
            );
        } catch (ClientException $e) {
            throw SalesforceAuthenticationException::fromClientException($e);
        }

        $response = json_decode($request->getBody(), true);

        if ($response) {
            $this->access_token = $response['access_token'];
            $this->instance_url = $response['instance_url'];
        }
    }

    public function setEndpoint($endPoint)
    {
        $this->endPoint = $endPoint;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function getInstanceUrl()
    {
        return $this->instance_url;
    }
}
