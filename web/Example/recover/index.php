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
require_once("../header.php");

if ($verify) $UserSystem->redirect301("../");

if (isset($_GET["blob"]) && isset($_POST["p"])) {
  $blob = $UserSystem->sanitize($_GET["blob"]);
  $UserSystem->recover($blob, $_POST["p"], $_POST["c"]);
  $UserSystem->redirect301("../login/?recovered");
}

if (isset($_POST["e"])) {
  $email = $UserSystem->sanitize($_POST["e"], "e");
  $UserSystem->sendRecover($email);
  $UserSystem->redirect301("../?recoverysent");
}
?>

  <div class="col-md-4 col-md-offset-4">
    <div class="well">
      <form class="form form-horizontal" method="post" action="">
        <?php if (!isset($_GET["blob"])): ?>
          <div class="form-group">
            <label for="e" class="col-sm-4 control-label">Email</label>
            <div class="col-sm-8">
              <input type="email" class="form-control" id="e" name="e">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
              <button type="submit" class="btn btn-default">Send Recovery Email</button>
              <a href="../login">Login</a>
            </div>
          </div>
        <?php elseif (isset($_GET["blob"])): ?>
          <div class="form-group">
            <label for="p" class="col-sm-4 control-label">Password</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" id="p" name="p">
            </div>
          </div>
          <div class="form-group">
            <label for="c" class="col-sm-4 control-label">Confirm</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" id="c" name="c">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
              <button type="submit" class="btn btn-default">Reset Email</button>
              <a href="../login">Login</a>
            </div>
          </div>
        <?php endif; ?>
      </form>
    </div>
  </div>

<?php require_once("../footer.php"); ?>