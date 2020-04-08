<?php
//Include composer packages
require_once 'vendor/autoload.php';

//Configuration for POP
require_once 'config.inc';

//Include game logic
require_once 'core/utilities/database.inc';
require_once 'core/utilities/utilities.inc';
require_once 'core/core.inc';
require_once 'core/games/struct.inc';
require_once 'core/games/browse.inc';

//Generate the base POP object
$pop = new pioneersOfPlymouth();