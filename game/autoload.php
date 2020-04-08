<?php
//Include composer packages
require_once 'vendor/autoload.php';

//Configuration for POP
require_once 'config.inc';

//Include game logic
require_once 'core/core.inc';
require_once 'play/play.inc';
require_once 'connection/ajaxFeed/ajaxFeed.inc';

//Generate the base POP object
$pop = new pioneersOfPlymouth();