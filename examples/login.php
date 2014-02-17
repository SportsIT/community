#!/usr/bin/php
<?php
require_once('../lib/SIT_API.php');
$API = 'http://api.sports-it.com/v3';
$privateKey = '565942e7beb5bc1f0fb20424b9eafbdd';
$publicKey = 'd3447f4e2f923fbb6249a693b54a53a6';

$username = 'CHANGEME';
$password = 'CHANGEME';


$postdata = http_build_query(
    array(
        'username' => $username,
        'password' => $password 
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context  = stream_context_create($opts);
$signedURL = SIT_API::getSignedURL($API.'/api/customer/login', $privateKey, $publicKey);
print_r(json_decode(file_get_contents($signedURL, false, $context)));

