<?php

//region Core
//region Utilities
//The location of the database host
if (!defined('DB_LOCATION')) define("DB_LOCATION",$sqlConnection['host']);
//The username of the database user
if (!defined('DB_USERNAME')) define("DB_USERNAME",$sqlConnection['user']);
//The password of the database user
if (!defined('DB_PASSWORD')) define("DB_PASSWORD",$sqlConnection['pass']);
//The name of the target database
if (!defined('DB_DATABASE')) define("DB_DATABASE",$sqlConnection['base']);
//endregion

//region Games
//Game browser maximum age
define("BROWSER_AGE_RESTRICTION", 24);
//endregion
//endregion

define("EXPANSION_ALLOWED_", true);