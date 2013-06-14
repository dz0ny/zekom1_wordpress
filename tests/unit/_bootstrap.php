<?php
// Here you can initialize variables that will for your tests

require_once(__DIR__."/../mockpress/mockpress.php");
require_once(__DIR__."/../../analitika.php");

$_SERVER['HTTP_ACCEPT']           = "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
$_SERVER['HTTP_ACCEPT_LANGUAGE']  = "sl-SI,sl;q=0.8,en-GB;q=0.6,en;q=0.4";
$_SERVER['HTTP_ACCEPT_ENCODING']  = "gzip,deflate,sdch";
$_SERVER['HTTP_USER_AGENT']       = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.36 Safari/537.36";
$_SERVER['REMOTE_ADDR']           = "123.123.123.123";
$_SERVER['REMOTE_PORT']           = "34567";
$_SERVER['SERVER_NAME']           = "www.ip-rs.si";