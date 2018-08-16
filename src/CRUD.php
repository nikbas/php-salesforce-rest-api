<?php

namespace bjsmasth\Salesforce;

use GuzzleHttp\Client;
use Exception\Salesforce as SalesforceException;

class CRUD
{
    /** @var string */
    protected $instanceUrl;

    /** @var string */
    protected $accessToken;

    /** @var string */
    protected $apiVersion;

    public function __construct(
        string $instanceUrl,
        string $accessToken,
        string $apiVersion = "v43.0"
    ) {
        $this->instanceUrl = $instanceUrl;
        $this->accessToken = $accessToken;
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

        return json_decode($request->getBody(), true);
    }

    public function create($object, array $data)
    {
        $url = "{$this->instanceUrl}/services/data/{$this->apiVersion}/sobjects/{$object}/";

        $client = new Client();

        $request = $client->request('POST', $url, [
            'headers' => [
                'Authorization' => "OAuth {$this->accessToken}",
                'Content-type' => 'application/json'
            ],
            'json' => $data
        ]);

        $status = $request->getStatusCode();

        if ($status != 201) {
            throw new SalesforceException(
                "Error: call to URL {$url} failed with status {$status}, response: {$request->getReasonPhrase()}"
            );
        }

        $response = json_decode($request->getBody(), true);

        return $response;

    }

    public function update($object, $id, array $data)
    {
        $url = "{$this->instanceUrl}/services/data/{$this->apiVersion}/sobjects/{$object}/{$id}";

        $client = new Client();

        $request = $client->request('PATCH', $url, [
            'headers' => [
                'Authorization' => "OAuth $this->accessToken",
                'Content-type' => 'application/json'
            ],
            'json' => $data
        ]);

        $status = $request->getStatusCode();

        if ($status != 204) {
            throw new SalesforceException(
                "Error: call to URL {$url} failed with status {$status}, response: {$request->getReasonPhrase()}"
            );
        }

        return $status;
    }

    public function upsert($object, $field, $id, array $data)
    {
        $url = "{$this->instanceUrl}/services/data/{$this->apiVersion}/sobjects/{$object}/{$field}/{$id}";

        $client = new Client();

        $request = $client->request('PATCH', $url, [
            'headers' => [
                'Authorization' => "OAuth {$this->accessToken}",
                'Content-type' => 'application/json'
            ],
            'json' => $data
        ]);

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

        $client = new Client();
        $request = $client->request('DELETE', $url, [
            'headers' => [
                'Authorization' => "OAuth {$this->accessToken}",
            ]
        ]);

        $status = $request->getStatusCode();

        if ($status != 204) {
            throw new SalesforceException(
                "Error: call to URL {$url} failed with status {$status}, response: {$request->getReasonPhrase()}"
            );
        }

        return true;
    }
}
