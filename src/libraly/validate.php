<?php

function isEmailDomainAllowed(string $email): bool {
    if(!isset($email) || empty($email)){
        throw new InvalidArgumentException("ERROR01 - Validate[isEmailDomainAllowed]: Email is not set or empty.");
        return false;
    }
    $domainWhiteList = ['ifsul.edu.br'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $parts = explode('@', $email);
    $domain = end($parts);
    return in_array($domain, $domainWhiteList, true);
}