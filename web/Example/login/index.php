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
if ($verify) $UserSystem->redirect("../");

if (isset($_POST["u"])) {
  $login = $UserSystem->logIn($_POST["u"], $_POST["p"]);
  if ($login === true) {
    $UserSystem->redirect("../?loggedin");
  } else {
    echo "Please tell someone this happened:<br>";
    var_dump($login);
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
        <li class="active"><a href="../login">Login</a></li>
        <li><a href="../register">Register</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <div class="col-md-4 col-md-offset-4">
    <div class="well">
      <form class="form form-horizontal" method="post" action="">
        <div class="form-group">
          <label for="u" class="col-sm-4 control-label">Username</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="u" name="u">
          </div>
        </div>
        <div class="form-group">
          <label for="p" class="col-sm-4 control-label">Password</label>
          <div class="col-sm-8">
            <input type="password" class="form-control" id="p" name="p">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-4 col-sm-8">
            <button type="submit" class="btn btn-default">Log in</button>
            <a href="../recover">Forgot Password</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js">
</script>
</body>
</html>