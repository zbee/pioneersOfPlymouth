<?php
require_once '/var/www/pop/web/assets/autoload.php';

if (!$isLoggedIn)
  $UserSystem->redirect('/user/login?mustBeLoggedIn');

$lobbyID = $_SERVER['REQUEST_URI'];
$lobbyID = str_replace('/game/', '', $lobbyID);
$pop->loadLobby($lobbyID);

if ($pop->lobbyLoaded !== true)
  $UserSystem->redirect('/dashboard?gameNonexistent');