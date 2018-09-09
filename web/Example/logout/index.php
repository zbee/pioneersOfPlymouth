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

if ($isLoggedIn) {
  if (isset($_GET["specific"])) {
    $logout = $UserSystem->logOut(
      $_GET["specific"], $session["username"], false
    );
    $UserSystem->redirect301("../");
  } elseif (isset($_GET["all"])) {
    $logout = $UserSystem->logOut(
      $_COOKIE[SITENAME],
      $session["id"],
      true,
      true
    );
    if ($logout === true)
      $UserSystem->redirect301("/");
  } else {
    $logout = $UserSystem->logOut(
      $_COOKIE[SITENAME], $session["username"], true
    );
    $UserSystem->redirect301("../");
  }
} else {
  $UserSystem->redirect301("../");
}