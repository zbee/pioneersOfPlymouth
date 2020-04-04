<?php
require_once "/var/www/pop/web/assets/autoload.php";

if ($isLoggedIn) {
  if (isset($_GET["specific"])) {
    $logout = $UserSystem->logOut(
      $_GET["specific"], $session["id"], false
    );
    $UserSystem->redirect301("/user/manage?sessionClosed");
  } elseif (isset($_GET["all"])) {
    $logout = $UserSystem->logOut(
      $_COOKIE[SITENAME],
      $session["id"],
      true,
      true
    );
    if ($logout === true)
      $UserSystem->redirect301("/user/login");
  } else {
    $logout = $UserSystem->logOut(
      $_COOKIE[SITENAME], $session["id"], true
    );
    $UserSystem->redirect301("/user/login");
  }
} else {
  $logout = $UserSystem->logOut(
    $_COOKIE[SITENAME], $session["id"], true
  );
  $UserSystem->redirect301("/");
}