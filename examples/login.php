#!/usr/bin/php
<?php
require_once('../lib/SIT_API.php');
$API = 'http://api.sports-it.com/v3';
$privateKey = 'CHANGEME';
$publicKey = '15bbb9d0bbf25e8d2978de1168c749dc';

$username = 'atippett@gmail.com';
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

