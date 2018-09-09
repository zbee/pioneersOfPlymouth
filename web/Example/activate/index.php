<?php
/*
This file is part of Zbee/UserSystem.

Zbee/UserSystem is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Zbee/UserSystem is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Zbee/UserSystem.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once "../../assets/autoload.php";
require_once("../../assets/php/UserSystem/config.php");

if (isset($_GET["blob"])) {
  $activate = $UserSystem->activateUser($_GET["blob"]);
  if ($activate === true) {
    $UserSystem->redirect301("../login");
  } else {
    echo "Please tell someone this happened:<br>";
    var_dump($activate);
  }
}

require_once("../../UserSystem/config.php");
?>
