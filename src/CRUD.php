<?php

namespace bjsmasth\Salesforce;

use bjsmasth\Salesforce\Exception\SalesforceException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class CRUD
{
    /** @var string */
    protected $instanceUrl;

    /** @var string */
    protected $accessToken;

    /** @var string */
    protected $apiVersion = "v44.0";

    public function getInstanceUrl(): string
    {
        return $this->instanceUrl;
    }

    public function setInstanceUrl(string $instanceUrl): void
    {
        $this->instanceUrl = $instanceUrl;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    public function setApiVersion(string $apiVersion): void
    {
        $this->apiVersion = $apiVersion;
    }

    public function query($query): array
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

    public function create($object, array $data): array
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

    public function update($object, $id, array $data): array
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

        $response = json_decode($request->getBody(), true);
        return $response;
    }

    public function upsert($object, $field, $id, array $data): array
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

        $response = json_decode($request->getBody(), true);
        return $response;
    }

    public function delete($object, $id): array
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

        $response = json_decode($request->getBody(), true);
        return $response;
    }
}
