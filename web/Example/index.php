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
require_once("header.php");

if ($verify === false) {
  $body = "You are not currently logged in."
    . "<br><br><a href='login' class='btn btn-block btn-default'>Login</a>";
} else {
  $body = "You are currently logged in as " . $session["username"] . "." .
    "<a href='edit' class='btn btn-block btn-default'>Edit</a>";
}
?>

  <div class="col-md-4 col-md-offset-4 text-center">
    <div class="well">
      <?= $body ?>
    </div>
  </div>

<?php require_once("footer.php"); ?>