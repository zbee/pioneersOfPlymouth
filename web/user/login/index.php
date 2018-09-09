<?php
require_once '/var/www/pop/web/assets/autoload.php';

if ($isLoggedIn)
  $UserSystem->redirect301("/dashboard?alreadyLoggedIn");

$error = '';

if (
  isset($_POST['user']) && isset($_POST['pass'])
  && !empty($_POST['user']) && !empty($_POST['pass'])
) {
  $user = $_POST['user'];
  $pass = $_POST['pass'];

  $login = $UserSystem->logIn($user, $pass);

  if ($login === true)
    $UserSystem->redirect301("/dashboard?loggedIn");
  else
    $error = $login;

  if (!empty($error)) {
    switch ($error) {
      case 'password':
        $error = 'Your password was not correct. This is case-sensitive.';
        break;
      case 'username':
        $error = 'A user with that name was not found.';
        break;
      case 'activate':
        $error = 'Your account has not yet been activated. Check your email.';
        break;
      case 'twoStep':
        $error = 'Two factor authentication is enabled. Check your email.';
        break;
      default:
        break;
    }

    $error = <<<Error
<div class="error">
    $error
</div>
Error;
  }
}

if (isset($_GET['registered']))
  $error = <<<PleaseActivate
<div class="error">
  You have successfully registered!<br>
  You have been sent an email with instructions to activate your account.
</div>
PleaseActivate;
?>

<div class="ribbon">
  <div class="mainContent">
    <form method="post" action="">
      <?=$error?><br><br>
      <label>
        Username
        <input type="text" name="user">
      </label>
      <br>
      <label>
        Password
        <input type="password" name="pass">
      </label>
      <br>
      <input type="submit" class="button" value="Login!">
      <br>
      <a href="../recover" class="smallText">Forgot?</a>
    </form>
  </div>
</div>