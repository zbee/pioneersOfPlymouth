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

$verify = $UserSystem->verifySession();
if ($verify !== true) $UserSystem->redirect("../");
$session = $UserSystem->session();

$error = "";

if (isset($_POST["p"])) {
  $_POST["c"] = hash("sha256", $_POST["c"] . $session["salt"]);
  if ($_POST["c"] === $session["password"]) {
    if ($_POST["p"] === $_POST["cp"]) {
      $salt = $UserSystem->createSalt($session["username"]);
      $pass = hash("sha256", $_POST["p"] . $salt);
      $UserSystem->dbUpd(
        [
          "users",
          [
            "salt" => $salt,
            "password" => $pass,
            "oldPassword" => $session["password"],
            "oldSalt" => $session["salt"],
            "passwordChanged" => time()
          ],
          [
            "username" => $session["username"]
          ]
        ]
      );
      $session["passwordChanged"] = time();
      $error = '
        <div class="alert alert-success">
          Password has been updated.
        </div>
      ';
    } else {
      $error = '
        <div class="alert alert-error">
          Passwords did not match.
        </div>
      ';
    }
  } else {
    $error = '
      <div class="alert alert-error">
        Current password is incorrect.
      </div>
    ';
  }
}

if (isset($_POST["e"])) {
  $_POST["c"] = hash("sha256", $_POST["c"] . $session["salt"]);
  if ($_POST["c"] === $session["password"]) {
    $e = $UserSystem->sanitize($_POST["e"]);
    $UserSystem->dbUpd(
      [
        "users",
        [
          "email" => $e,
          "oldEmail" => $session["email"],
          "emailChanged" => time()
        ],
        [
          "username" => $session["username"]
        ]
      ]
    );
    $session["email"] = $e;
    $session["emailChanged"] = time();
    $error = '
      <div class="alert alert-success">
        Password has been updated.
      </div>
    ';
  } else {
    $error = '
      <div class="alert alert-error">
        Current password is incorrect.
      </div>
    ';
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Zbee/UserSystem</title>

  <!-- Bootstrap core CSS -->
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"
        rel="stylesheet">
  <style>body {
      margin-top: 75px;
    }</style>

  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed"
              data-toggle="collapse" data-target="#navbar" aria-expanded="false"
              aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="../">Zbee/UserSystem</a>
    </div>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li><a href="../">Home</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="../logout">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <div class="col-md-4 col-md-offset-4">
    <?= $error ?>
    <div class="well text-center">
      <b>Update password</b>
      <br>
      <?= $session["passwordChanged"] == 0 ? "Never changed" :
        "Last changed " . date("Y-m-d\THi", $session["passwordChanged"]) ?>
      <br><br>
      <form class="form form-horizontal text-left" method="post" action="">
        <div class="form-group">
          <label for="c" class="col-xs-12 col-sm-4 control-label">
            Current
          </label>
          <div class="col-xs-12 col-sm-8">
            <input type="password" class="form-control" id="c" name="c">
          </div>
        </div>
        <br>
        <div class="form-group">
          <label for="p" class="col-xs-12 col-sm-4 control-label">
            New
          </label>
          <div class="col-xs-12 col-sm-8">
            <input type="password" class="form-control" id="p" name="p">
          </div>
        </div>
        <div class="form-group">
          <label for="cp" class="col-xs-12 col-sm-4 control-label">
            Confirm
          </label>
          <div class="col-xs-12 col-sm-8">
            <input type="password" class="form-control" id="cp" name="cp">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12 text-center">
            <button type="submit" class="btn btn-default">
              Update Password
            </button>
          </div>
        </div>
      </form>
    </div>
    <br>
    <div class="well text-center">
      <b>Update email</b>
      <br>
      <?= ($session["emailChanged"] == 0 ? "Never changed" :
        "Last changed " . date("Y-m-d\THi", $session["emailChanged"]))
      . "<Br>Is currently " . $session["email"] ?>
      <br><br>
      <form class="form form-horizontal text-left" method="post" action="">
        <div class="form-group">
          <label for="c" class="col-xs-12 col-sm-4 control-label">
            Password
          </label>
          <div class="col-xs-12 col-sm-8">
            <input type="password" class="form-control" id="c" name="c">
          </div>
        </div>
        <br>
        <div class="form-group">
          <label for="e" class="col-xs-12 col-sm-4 control-label">
            New Email
          </label>
          <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="e" name="e">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12 text-center">
            <button type="submit" class="btn btn-default">
              Update Email
            </button>
          </div>
        </div>
      </form>
    </div>
    <br>
    <div class="well text-center">
      <b>Sessions</b>
      <br>
      <?php
      $rows = $UserSystem->dbSel(
        ["userblobs", ["user" => $session["username"], "action" => "session"]]
      );
      echo "You have $rows[0] active sessions."
        . '<br><br><a href="../logout?all" class="btn btn-block btn-default">'
        . "Log out all sessions</a><br><br>";
      if ($rows[0] > 0) {
        echo '<table class="table table-responsive table-striped
            tabled-bordered">';
        foreach ($rows as $key => $row) {
          if ($key === 0) continue;
          $row["date"] = date("Y-m-d\THi", $row["date"]);
          echo "
              <tr>
                <td>$row[date]</td>
                <td>
                  <a href='../logout?specific=$row[code]'>
                    <i class='glyphicon glyphicon-remove'></i>
                  </a>
                </td>
              </tr>
            ";
        }
        echo "</table>";
      }
      ?>
    </div>
  </div>
</div>

<script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js">
</script>
</body>
</html>