<?php
require_once '/var/www/pop/keys.php';
require_once '/var/www/pop/web/assets/php/UserSystem/config.php';


$isLoggedIn = false;
$verify = $UserSystem->verifySession();
if ($verify) $session = $UserSystem->session();

if (isset($session) && is_array($session))
  $isLoggedIn = true;

if (!isset($include_header) || $include_header === true)
  require_once '/var/www/pop/web/assets/php/header.php';