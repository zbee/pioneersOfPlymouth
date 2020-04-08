<?php
require_once '/var/www/pop/web/assets/autoload.php';

if (!$isLoggedIn)
  $UserSystem->redirect('/user/login?mustBeLoggedIn');

$gameID = $_SERVER['REQUEST_URI'];
$gameID = str_replace('/game/', '', $gameID);
$gameID = (int) $gameID;

$gameLoad = $UserSystem->dbSel(['gameLobbies', ['id' => $gameID]]);

if ($gameLoad[0] !== 1)
  $UserSystem->redirect('/dashboard?gameNonexistent');

$game = $gameLoad[1];
var_dump($game);