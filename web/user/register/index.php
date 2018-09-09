<?php
require_once '/var/www/pop/web/assets/autoload.php';

if ($isLoggedIn)
  $UserSystem->redirect301("/dashboard?alreadyLoggedIn");

$error = '';

if (
  isset($_POST['user']) && isset($_POST['pass'])
  && !empty($_POST['user']) && !empty($_POST['pass'])
) {
  $user  = $_POST['user'];

  $alias = $_POST['alias'];
  if ($alias === $user)
    unset($alias);

  $pass  = $_POST['pass'];
  $conf  = $_POST['conf'];
  if ($pass !== $conf)
    $error = 'unequalPasses';

  $mail  = $UserSystem->sanitize($_POST['mail'], 'e');
  if ($mail === 'FAILED')
    $error = 'badEmail';

  if (empty($error))
    $register = $UserSystem->addUser($user, $pass, $mail);

  if (is_int($register))
    $UserSystem->redirect301("/user/login?registered");
  else
    $error = $register;

  if (!empty($error)) {
    switch ($error) {
      case 'unequalPasses':
        $error = 'Your passwords do not match.';
        break;
      case 'badEmail':
        $error = 'Your email does not look real;
        it must be similar to example@example.com.';
        break;
      case 'email':
        $error = 'You already have an account with this email address.';
        break;
      case 'username':
        $error = 'You already have an account with this username.';
        break;
    }

    $error = <<<Error
<div class="error">
  $error
</div>
Error;
  }
}
?>

<div class="ribbon">
  <div class="mainContent">
    <form method="post" action="">
      <?=$error?>
      <label>
        Username
        <input type="text" name="user">
      </label>
      <label>
        Alias
        <input type="text" name="alias">
      </label>
      <label>
        Email
        <input type="text" name="mail">
      </label>
      <hr>
      <label>
        Password
        <input type="password" name="pass">
      </label>
      <label>
        Confirm
        <input type="password" name="conf">
      </label>
      <input type="submit" class="button" value="Register!">
    </form>
  </div>
</div>