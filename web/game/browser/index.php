<?php
require_once '/var/www/pop/web/assets/autoload.php';

if (!$isLoggedIn)
  $UserSystem->redirect301('/user/login?mustBeLoggedIn');

$currentTimestamp = (new DateTime())->getTimestamp();
$ageRestriction = $currentTimestamp - (60 * 60 * 2);


$games = $UserSystem->dbSel(
  [
    'gameLobbies',
    ['date' => ['>', $ageRestriction]],
    ['name', 'asc']
  ]
);

var_dump($games);