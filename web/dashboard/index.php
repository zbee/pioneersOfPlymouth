<?php
require_once '/var/www/pop/web/assets/autoload.php';

if (!$isLoggedIn)
  $UserSystem->redirect301('/user/login?mustBeLoggedIn');
?>

<div class="ribbon">
  <div class="mainContent">
    <span class="largestText">Welcome In</span>

    <div class="tabHolder">

      <a class="tab" href="/game/browser">
        <span class="header">
          Game Browser
        </span>
        <div class="content">
          <div>
            131
          </div>
          <div>
            Games Open
          </div>
        </div>
      </a>

      <div class="deadSpace"></div>

      <a class="tab">
        <span class="header">
          Statistics
        </span>
        <div class="content">
          <div>
            S
          </div>
          <div>
            Skill Rank
          </div>
        </div>
      </a>

      <a class="tab">
        <span class="header">
          Friends
        </span>
        <div class="content">
          <div>
            131
          </div>
          <div>
            Games Open
          </div>
        </div>
      </a>

      <div class="deadSpace"></div>

      <a class="tab">
        <span class="header">
          Account
        </span>
        <div class="content">
          <div>
            S
          </div>
          <div>
            Skill Rank
          </div>
        </div>
      </a>

    </div>

  </div>
</div>
