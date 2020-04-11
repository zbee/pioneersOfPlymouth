<?php
require_once '/var/www/pop/web/assets/autoload.php';

if (!$isLoggedIn)
  $UserSystem->redirect('/user/login?mustBeLoggedIn');
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
          Your Statistics
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
            2
          </div>
          <div>
            Companions Online
          </div>
        </div>
      </a>

      <div class="deadSpace"></div>

      <a class="tab">
        <span class="header">
          Leader Board
        </span>
        <div class="content">
          <div>
            #475
          </div>
          <div>
            Position
          </div>
        </div>
      </a>

      <a class="tab">
        <span class="header">
          Your History
        </span>
        <div class="content">
          <div>
            4
          </div>
          <div>
            Games Finished
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
            832d
          </div>
          <div>
            of Membership
          </div>
        </div>
      </a>

    </div>

  </div>
</div>
