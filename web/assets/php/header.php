<!DOCTYPE html>
<html>
<head>
  <title>Pioneers of Plymouth</title>
  <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>

<header>
  <a href="/">Home</a>
  <div class="right">
    <?php if ($isLoggedIn): ?>
      <a href="/user/logout">Logout</a>
    <?php else: ?>
      <a href="/user/login">Login</a>
      or
      <a href="/user/register">Sign Up</a>
    <?php endif; ?>
  </div>
</header>