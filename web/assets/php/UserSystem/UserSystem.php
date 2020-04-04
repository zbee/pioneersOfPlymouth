<?php
/**
* System class for performing common user system operations.
*
* @package    UserSystem
* @author     Ethan Henderson <ethan@zbee.me>
* @copyright  Copyright 2014-2015 Ethan Henderson
* @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link       https://github.com/zbee/usersystem
* @since      Class available since Release 0.1
*/
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

class UserSystem extends UserUtils {

  /**
  * Returns an array full of the data about a user
  * Example: $UserSystem->session(972)
  *
  * @access public
  * @param mixed $session
  * @return mixed
  */
  public function session ($session = false) {
    if (!$session) {
      if (!isset($_COOKIE[SITENAME])) return "cookie";
      $session = filter_var(
        $_COOKIE[SITENAME],
        FILTER_SANITIZE_FULL_SPECIAL_CHARS
      );
      $time = strtotime('+30 days');
      $query = $this->dbSel(
        [
          "userblobs",
          [
            "code" => $session,
            "date" => ["<", $time],
            "action" => "session"
          ]
        ]
      );
      if ($query[0] === 1) {
        $identifier = $query[1]['user'];
        $query = $this->dbSel(["users", ["id" => $identifier]]);
        if ($query[0] === 1) return $query[1];
      } else {
        $this->logOut($session, null, true);
        return "blob";
      }
    } else {
      $query = $this->dbSel(["users", ["id" => $session]]);
      if ($query[0] === 1) return $query[1];
    }

    return false;
  }

  /**
   * Verifies a user's session
   * Example: $UserSystem->verifySession($_COOKIE[SITENAME])
   *
   * @access public
   * @param mixed $session
   * @return mixed
   */
  public function verifySession ($session = false) {
    if (!isset($_COOKIE[SITENAME])) return false;
    $COOKIE = filter_var(
      $_COOKIE[SITENAME],
      FILTER_SANITIZE_FULL_SPECIAL_CHARS
    );
    if (!$session) $session = $COOKIE;
    $session = $this->sanitize($session, "s");

    $tamper  = substr($session, -32);
    $time = strtotime("+30 days");
    $stmt = $this->dbSel(
      [
        "userblobs",
        [
          "code" => $session,
          "date" => ["<", $time],
          "action" => "session"
        ]
      ]
    );

    $rows = $stmt[0];
    if ($rows == 1) {
      $identifier = $stmt[1]['user'];
      $select = $this->dbSel(["users", ["id" => $identifier]]);
      if ($select[0] !== 1) return "user";
      if (md5($select[1]["salt"].substr($session, 0, 128)) == $tamper) {
        $ban = $this->checkBan($identifier);
        if ($ban === false) return true;
        return "ban";
      } else {
        $this->dbDel(["userblobs", ["code"=>$session, "action"=>"session"]]);
        return "tamper";
      }
    }
    return "session";
  }

  /**
   * Inserts a new user
   * Example: $UserSystem->addUser("Bob","jg85h58gh58","bob@example.com")
   *
   * @access public
   * @param string $username
   * @param string $password
   * @param string $email
   * @param mixed $more
   * @return mixed
   */
   public function addUser ($username, $password, $email, $more = false) {
     $username = $this->sanitize($username, "s");
     $email = $this->sanitize($email, "e");
     $usernameUse = $this->dbSel(["users", ["username" => $username]])[0];
     if ($usernameUse == 0) {
       $emailUse = $this->dbSel(["users", ["email" => $email]])[0];
       if ($emailUse == 0) {
         $salt = $this->createSalt();
         $data = [
           "username" => $username,
           "password" => hash("sha256", $password.$salt),
           "email" => $email,
           "salt" => $salt,
           "dateRegistered" => time()
         ];

         $morech = $this->sanitize($more, "b");

         if ($morech !== false && is_array($more))
           foreach ($more as $item)
             $data[array_search($item, $more)] = $item;

         $identifier = $this->dbIns(["users", $data]);

         if ($identifier !== 1)
           return "queryFailed";

         $blob = $this->insertUserBlob($identifier, "activate");
         $link = $this->sanitize(
           URL_PREFACE."://".DOMAIN."/".ACTIVATE_PG."/?blob=$blob",
           "u"
         );
         $this->sendMail(
           $email,
           "Activate your ".SITENAME." account",
           "           Hello $username

           To activate your account, click the link below.
           $link

           ======

           If this wasn't you, you can ignore this email.

           Thank you"
         );

         return $identifier;
       } else {
         return "email";
       }
     } else {
       return "username";
     }
   }

  /**
   * Activates a new user's account
   * Example: $UserSystem->activateUser("mrogjsruicyu78chsr87thmrsu")
   *
   * @access public
   * @param string $code
   * @return boolean
   */
  public function activateUser ($code) {
    $code = $this->sanitize($code, "s");
    $rows = $this->dbSel(["userblobs", ["code"=>$code, "action"=>"activate"]]);
    if ($rows[0] == 1) {
      $user = $rows[1]["user"];
      $update = $this->dbUpd(["users", ["activated"=>1], ["id"=>$user]]);
      if ($update !== true) return "UpdateFailed";
      $noActiv = $this->dbSel(["users", ["id"=>$user,"activated"=>0]])[0];
      if ($noActiv !== 0) return "NotActivated";
      $del=$this->dbDel(["userblobs", ["code"=>$code, "action"=>"activate"]]);
      if ($del !== true) return "DeleteFailed";
      $blob=$this->dbSel(["userblobs",["code"=>$code,"action"=>"activate"]])[0];
      if ($blob !== 0) return "BlobNotRemoved";
      return true;
    }
    return "InvalidBlob";
  }

  /**
   * Logs in a user
   * Example: $UserSystem->logIn("Bob", "Bob's Password")
   *
   * @access public
   * @param string $username
   * @param string $password
   * @return boolean
   */
  public function logIn ($username, $password, $ignoreTS = false) {
    $ignoreTS = $this->sanitize($ignoreTS, "b");
    $ipAddress = $this->getIP();
    $username = $this->sanitize($username, "s");
    $user = $this->dbSel(["users", ["username" => $username]]);
    if ($user[0] == 1) {
      $user = $user[1];
      $password = hash("sha256", $password.$user["salt"]);
      $oldPassword = hash("sha256", $password.$user["salt"]);
      if ($password == $user["password"]) {
        if ($user["activated"] == 1) {
          if ($user["twoStep"] == 0 || $ignoreTS !== false) {
            if (ENCRYPTION === true) $ipAddress=encrypt($ipAddress,$user["id"]);
            $this->dbUpd(
              [
                "users",
                [
                  "ip" => $ipAddress,
                  "lastLoggedIn" => time(),
                  "oldLastLoggedIn" => $user["lastLoggedIn"]
                ],
                [
                  "id" => $user["id"]
                ]
              ]
            );
            $hash = $this->insertUserBlob($user["id"]);
            if (!headers_sent()) {
              setcookie(
                SITENAME,
                $hash,
                strtotime('+30 days'),
                "/",
                DOMAIN_SIMPLE
              );
            }
            return true;
          } else {
            return "twoStep";
          }
        } else {
          return "activate";
        }
      } else {
        if ($password == $oldPassword) return "oldPassword";
        return "password";
      }
    }
    return "username";
  }

  /**
   * Logs out a selected userblob or group of user blobs
   * Example: $UserSystem->logOut($_COOKIE[$sitename], "Bob", true)
   * @access public
   *
   * @param string $code
   * @param mixed int $user
   * @param mixed  $curSess
   * @param mixed  $all
   *
   * @return boolean
   */
  public function logOut ($code, $user = false, $curSess = false, $all = false) {
    if ($user == false && $curSess == false) return 'needData';

    $code = $this->sanitize($code, "s");
    $curSess = $this->sanitize($curSess, "b");
    $all = $this->sanitize($all, "b");

    if ($curSess === true) {
      setcookie(
        SITENAME,
        null,
        strtotime('-60 days'),
        "/",
        DOMAIN_SIMPLE
      );
    }

    if (!$all) {
      return $this->dbDel(
        [
          "userblobs",
          [
            "code" => $code,
            "user" => $user,
            "action" => "session"
          ]
        ]
      );
    } else {
      return $this->dbDel(["userblobs", ["user"=>$user]]);
    }

    return false;
  }

  /**
   * Finishes logging a user in if they have twoStep enabled.
   * Example: $UserSystem->twoStep($blob)
   *
   * @access public
   * @param string $code
   * @return mixed
   */
  public function twoStep ($code) {
    $code = $this->sanitize($code, "s");
    $return = "code";
    $select = $this->dbSel(["userblobs", ["code"=>$code, "action"=>"twoStep"]]);
    if ($select[0] === 1) {
      if ($select[1]["date"] > time() - 3600) {
        $this->logIn(
          $select[1]["user"],
          $this->session($select[1]["user"])["password"],
          true
        );
        $return = true;
      } else {
        $return = "expired";
      }
    }

    $this->dbDel(["userblobs", ["code"=>$code, "action"=>"twoStep"]]);
    return $return;
  }

  /**
  * Allows a user to reset their pass using the link received from sendRecover
  * Example: $UserSystem->recover("fmg49t4c8u5ym8598yv5")
  *
  * @access public
  * @param string $blob
  * @param string $pass
  * @param string $passconf
  * @return mixed
  */
  public function recover ($blob, $pass, $passconf) {
    $blob = $this->sanitize($blob, "s");
    if ($pass === $passconf) {
      $select = $this->dbSel(
        [
          "userblobs",
          [ "code"=>$blob, "action"=>"recovery" ]
        ]
      );
      if ($select[0] == 1) {
        $user = $select[1]["user"];
        $salt = $this->session($user)["salt"];
        $pass = hash("sha256", $pass.$salt);
      }
    } else {
      return "password";
    }
  }
}
