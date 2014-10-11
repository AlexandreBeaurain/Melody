<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
$localNetworks = array('::1'=>8,'127.0.0.0'=>8,'192.168.0.0'=>16,'85.169.47.232'=>32);
function ipAddressInNetwork($ip, $net_addr, $net_mask){
    if($net_mask <= 0){ return false; }
    $ip_binary_string = sprintf("%032b",ip2long($ip));
    $net_binary_string = sprintf("%032b",ip2long($net_addr));
    return (substr_compare($ip_binary_string,$net_binary_string,0,$net_mask) === 0);
}
$private = false;
foreach( $localNetworks as $localNetwork => $mask ) {
    if ( ipAddressInNetwork( @$_SERVER['REMOTE_ADDR'],$localNetwork,$mask) ) {
        $private = true;
        break;
    }
}

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || ! $private
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
Debug::enable();

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
