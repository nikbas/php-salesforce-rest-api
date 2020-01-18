# PHP Salesforce REST API wrapper

Forked from:
```bjsmasth/php-salesforce-rest-api``` ```Cleeng/php-salesforce-rest-api``` ```jerkob/php-salesforce-rest-api-forked```

## Install

Via **[composer](https://getcomposer.org/)**

``` bash
composer require ehaerer/php-salesforce-rest-api
```

# Getting Started

Setting up a Connected App

1. Log into your Salesforce org
2. Click on Setup in the upper right-hand menu
3. Under Build click ```Create > Apps ```
4. Scroll to the bottom and click ```New``` under Connected Apps.
5. Enter the following details for the remote application:
    - Connected App Name
    - API Name
    - Contact Email
    - Enable OAuth Settings under the API dropdown
    - Callback URL
    - Select access scope (If you need a refresh token, specify it here)
6. Click Save

After saving, you will now be given a _consumer key_ and _consumer secret_. Update your config file with values for ```consumerKey``` and ```consumerSecret```

# Setup

Authentication

```bash
    $options = [
        'grant_type' => 'password',
        'client_id' => 'CONSUMERKEY', /* insert consumer key here */
        'client_secret' => 'CONSUMERSECRET', /* insert consumer secret here */
        'username' => 'SALESFORCE_USERNAME', /* insert Salesforce username here */
        'password' => 'SALESFORCE_PASSWORD' . 'SECURITY_TOKEN' /* insert Salesforce user password and security token here */
    ];

    $salesforce = new \EHAERER\Salesforce\Authentication\PasswordAuthentication($options);
    /* if you want to login to a Sandbox change the url to https://test.salesforce.com/ */
    $endPoint = 'https://login.salesforce.com/';
    $salesforce->setEndpoint($endPoint);
    $salesforce->authenticate();

    /* if you need access token or instance url */
    $accessToken = $salesforce->getAccessToken();
    $instanceUrl = $salesforce->getInstanceUrl();
```

Query

```bash
    $query = 'SELECT Id,Name FROM ACCOUNT LIMIT 100';

    $salesforceFunctions = new \EHAERER\Salesforce\SalesforceFunctions($instanceUrl, $accessToken);
    /* returns array with the queried data */
    $data = $salesforceFunctions->query($query);
```

Create

```bash

    $data = [
       'Name' => 'Some name',
    ];

    /* returns the id of the created object */
    $salesforceFunctions->create('Account', $data);
```

Update

```bash
    $new_data = [
       'Name' => 'another name',
    ];

    /* returns statuscode */
    $salesforceFunctions->update('Account', $id, $new_data);

```

Delete

```bash
    $salesforceFunctions->delete('Account', $id);

```


#### Changelog: ####
##### 18.01.2020 #####
 - switched to PHP >7.0
 - renamed class from CRUD to SalesforceFunctions
 - added dependency to ext-json in composer package
