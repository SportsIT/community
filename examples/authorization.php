#!/usr/bin/php
<?php
require_once('../lib/SIT_API.php');
$API = 'http://api.sports-it.com/v3';
$privateKey = 'CHANGE_ME';
$publicKey = 'CHANGE_ME';

$signedURL = SIT_API::getSignedURL($API.'/api/season/search?leagues=true&teams=true', $privateKey, $publicKey);

print_r(json_decode(file_get_contents($signedURL)));

