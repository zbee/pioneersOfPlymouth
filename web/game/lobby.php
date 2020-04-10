<?php
require_once '/var/www/pop/web/assets/autoload.php';

if (!$isLoggedIn)
  $UserSystem->redirect('/user/login?mustBeLoggedIn');

$lobbyID = $_SERVER['REQUEST_URI'];
//@todo: this needs switched to an if statement for game vs lobby
$lobbyID = str_replace(URL_SCHEME_LOBBY, '', $lobbyID);
$lobbyID = str_replace(URL_SCHEME_JOIN_LOBBY, '', $lobbyID);
$lobbyID = str_replace(URL_SCHEME_GAME, '', $lobbyID);
$lobbyID = str_replace(URL_SCHEME_CONNECT_GAME, '', $lobbyID);
$lobbyID = str_replace(URL_SCHEME_POST_GAME, '', $lobbyID);
$pop->loadLobby($lobbyID);

if ($pop->lobbyLoaded !== true)
  $UserSystem->redirect('/dashboard?gameNonexistent');
?>

<div class="ribbon balance">
  <div>content</div>
  <div>not content</div>
</div>
