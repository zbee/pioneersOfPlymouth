<?php

//region Core
//region Utilities
//The location of the database host
if (!defined('DB_LOCATION'))
  define("DB_LOCATION", $sqlConnection['host']);
//The username of the database user
if (!defined('DB_USERNAME'))
  define("DB_USERNAME", $sqlConnection['user']);
//The password of the database user
if (!defined('DB_PASSWORD'))
  define("DB_PASSWORD", $sqlConnection['pass']);
//The name of the target database
if (!defined('DB_DATABASE'))
  define("DB_DATABASE", $sqlConnection['base']);
//endregion

//region Games
//Game browser maximum age
define('BROWSER_LOBBY_AGE_RESTRICTION', 99999999);

define('URL_SCHEME_LOBBY', '/game/lobby/');
define('URL_SCHEME_JOIN_LOBBY', '/game/join/');
define('URL_SCHEME_GAME', '/game/');
define('URL_SCHEME_CONNECT_GAME', '/game/connect/');
define('URL_SCHEME_POST_GAME', '/game/post/');

define('CONNECTION_TYPE_BROWSER', 1);
define('CONNECTION_TYPE_LOBBY', 2);
define('CONNECTION_TYPE_GAME', 3);
define('CONNECTION_TYPE_POST_GAME', 4);
//endregion
//endregion

define('EXPANSION_ALLOWED_', true);