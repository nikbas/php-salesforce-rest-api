<?php

namespace jerkob\Salesforce\Authentication;

interface AuthenticationInterface
{
    public function getAccessToken();
    public function getInstanceUrl();
}
