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
$lobby = $pop->getLobby();
$lobby->loadPlayers($pop);
?>

<div class="ribbon balance" id="title">
  <div class="mainContent"><h2><?= $lobby->name ?></h2></div>
  <div>by <a href="#"><?= $lobby->owner->username ?></a></div>
</div>
<div class="ribbon evenLeft small" id="basic_info">
  <div>
    Invite Only: <?= $lobby->inviteOnlyText ?>
    &nbsp; &nbsp; - &nbsp; &nbsp;
    Language: <?= $lobby->language ?>
  </div>
  <div>
    Lobby ID:
    <small><?= $lobby->uuid ?></small>
    <sup><a onclick="copyToClipboard('<?= $lobby->uuid ?>')">(copy)</a></sup>
  </div>
</div>
<div class="ribbon even" id="players_settings">
  <div>
    <?php foreach ($lobby->players as $player): ?>
      <?= $player->username ?><br>
    <?php endforeach; ?>
  </div>
  <div class="mainContent"></div>
</div>

<script>
  const copyToClipboard = str => {
    const el = document.createElement('textarea');
    el.value = str;
    el.setAttribute('readonly', '');
    el.style.position = 'absolute';
    el.style.left = '-9999px';
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
  };
</script>