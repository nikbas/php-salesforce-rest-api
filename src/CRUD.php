<?php

namespace jerkob\Salesforce;

use jerkob\Salesforce\Exception\SalesforceException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class CRUD
{
    /** @var string */
    protected $instanceUrl;

    /** @var string */
    protected $accessToken;

    /** @var string */
    protected $apiVersion = "v47.0";
    
    public function __construct($instanceUrl = NULL, $accessToken = NULL)
    {
        if ($instanceUrl)
        {
            $this->setInstanceUrl($instanceUrl);
        }
        
        if ($accessToken)
        {
            $this->setAccessToken($accessToken);
        }
    }

    public function getInstanceUrl()
    {
        return $this->instanceUrl;
    }

    public function setInstanceUrl($instanceUrl)
    {
        $this->instanceUrl = $instanceUrl;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    public function query($query)
    {
        $url = "{$this->instanceUrl}/services/data/{$this->apiVersion}/query";

        $client = new Client();
        $request = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => "OAuth {$this->accessToken}"
            ],
            'query' => [
                'q' => $query
            ]
        ]);

        $response = json_decode($request->getBody(), true);
        return $response;
    }

    public function retrieve($object, $field, $id)
    {
        $url = "{$this->instanceUrl}/services/data/{$this->apiVersion}/sobjects/{$object}/{$field}/{$id}";

        $client = new Client();

        try {
            $request = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => "OAuth {$this->accessToken}",
                    'Content-type' => 'application/json'
                ],
            ]);
        } catch (ClientException $e) {
            throw SalesforceException::fromClientException($e);
        }

        $status = $request->getStatusCode();

        if ($status != 200) {
            throw new SalesforceException(
                "Error: call to URL {$url} failed with status {$status}, response: {$request->getReasonPhrase()}"
            );
        }

        $response = json_decode($request->getBody(), true);
        return $response;
    }

    public function create($object, $data)
    {
        $url = "{$this->instanceUrl}/services/data/{$this->apiVersion}/sobjects/{$object}/";

        $client = new Client();

        try {
            $request = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => "OAuth {$this->accessToken}",
                    'Content-type' => 'application/json'
                ],
                'json' => $data
            ]);

            $status = $request->getStatusCode();
        } catch (ClientException $e) {
            throw SalesforceException::fromClientException($e);
        }

        if ($status != 201) {
            throw new SalesforceException(
                "Error: call to URL {$url} failed with status {$status}, response: {$request->getReasonPhrase()}"
            );
        }

        $response = json_decode($request->getBody(), true);
        return $response;
    }

    public function update($object, $id, $data)
    {
        $url = "{$this->instanceUrl}/services/data/{$this->apiVersion}/sobjects/{$object}/{$id}";

        $client = new Client();

        try {
            $request = $client->request('PATCH', $url, [
                'headers' => [
                    'Authorization' => "OAuth $this->accessToken",
                    'Content-type' => 'application/json'
                ],
                'json' => $data
            ]);
        } catch (ClientException $e) {
            throw SalesforceException::fromClientException($e);
        }

        $status = $request->getStatusCode();

        if ($status != 204) {
            throw new SalesforceException(
                "Error: call to URL {$url} failed with status {$status}, response: {$request->getReasonPhrase()}"
            );
        }

        return $status;
    }

    public function upsert($object, $field, $id, $data)
    {
        $url = "{$this->instanceUrl}/services/data/{$this->apiVersion}/sobjects/{$object}/{$field}/{$id}";

        $client = new Client();

        try {
            $request = $client->request('PATCH', $url, [
                'headers' => [
                    'Authorization' => "OAuth {$this->accessToken}",
                    'Content-type' => 'application/json'
                ],
                'json' => $data
            ]);
        } catch (ClientException $e) {
            throw SalesforceException::fromClientException($e);
        }

        $status = $request->getStatusCode();

        if ($status != 204 && $status != 201) {
            throw new SalesforceException(
                "Error: call to URL {$url} failed with status {$status}, response: {$request->getReasonPhrase()}"
            );
        }

        return $status;
    }

    public function delete($object, $id)
    {
        $url = "{$this->instanceUrl}/services/data/{$this->apiVersion}/sobjects/{$object}/{$id}";

        try {
            $client = new Client();
            $request = $client->request('DELETE', $url, [
                'headers' => [
                    'Authorization' => "OAuth {$this->accessToken}",
                ]
            ]);
        } catch (ClientException $e) {
            throw SalesforceException::fromClientException($e);
        }

        $status = $request->getStatusCode();

        if ($status != 204) {
            throw new SalesforceException(
                "Error: call to URL {$url} failed with status {$status}, response: {$request->getReasonPhrase()}"
            );
        }

        return true;
    }
}
