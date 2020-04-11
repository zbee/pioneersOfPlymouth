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

<div class="ribbon balance small" id="title">
  <div class="mainContent"><h2><?= $lobby->name ?></h2></div>
  <div>by <a href="#"><?= $lobby->owner->username ?></a></div>
</div>
<div class="ribbon evenLeft small" id="basic_info">
  <div class="smallText">
    Invite Only: <?= $lobby->inviteOnlyText ?>
    &nbsp; &nbsp; - &nbsp; &nbsp;
    Language: <?= $lobby->language ?>
  </div>
  <div class="smallText">
    Lobby ID:
    <small><?= $lobby->uuid ?></small>
    <sup><a onclick="copyToClipboard('<?= $lobby->uuid ?>')">(copy)</a></sup>
  </div>
</div>
<div class="ribbon even small" id="players_settings">
  <div>
    <?php foreach ($lobby->players as $player): ?>
      <?= $player->username ?><br>
    <?php endforeach; ?>
  </div>
  <div class="mainContent">
    <span class="bigText">Map Style</span>: <a href="#">Original</a>
    &nbsp; &nbsp; - &nbsp; &nbsp;
    <span class="bigText">Scenario</span>: <a href="#">None</a>
    <br>
    <span class="bigText">Trade Rules</span>: <a href="#">Standard</a>
    &nbsp; &nbsp; - &nbsp; &nbsp;
    <span class="bigText">Robber Occurrence</span>: <a href="#">Standard</a>
    <br>
    <span class="bigText">Tile Set</span>: <a href="#">Designed</a>
    &nbsp; &nbsp; - &nbsp; &nbsp;
    <span class="bigText">Board Design</span>: <a href="#">Designed</a>
    <br>
    <a href="#" class="text">Edit Game Settings</a>
  </div>
</div>
<div class="ribbon focusText">
  <div class="mainContent">
    <h2><a href="#">Start Game!</a></h2>
  </div>
  <div class="chat">
    <span id="gameChat">
      Welcome to the lobby!
    </span>
    <label>
      <?= $pop->user->username ?>
      <input type="text" id="chatBox" placeholder="Type a message" />
      <a href="#">Send!</a>
    </label>
  </div>
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