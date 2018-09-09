<?php
/**
* Class full of utility methods for working with users.
*
* @package    UserSystem
* @author     Ethan Henderson <ethan@zbee.me>
* @copyright  Copyright 2014-2015 Ethan Henderson
* @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link       https://github.com/zbee/usersystem
* @since      Class available since Release 0.96
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

class UserUtils extends Database {

  /**
  * Encrypts any data and makes it only decryptable by the same user.
  * Example: $UserSystem->encrypt("myEmail", "bob")
  *
  * @access public
  * @param string $decrypted
  * @param int $identifier
  * @return mixed
  */
  public function encrypt ($decrypted, $identifier) {
    $salt = $this->dbSel(["users", ["id" => $identifier]]);
    if ($salt[0] == 0) return false;
    $key = hash('SHA256', $salt[1]["salt"], true);
    srand();
    $initVector = mcrypt_create_iv(
      mcrypt_get_iv_size(
        MCRYPT_RIJNDAEL_128,
        MCRYPT_MODE_CBC
      ),
      MCRYPT_RAND
    );
    if (strlen($iv_base64 = rtrim(base64_encode($initVector), '=')) != 22)
      return false;
    $encrypted = base64_encode(
      mcrypt_encrypt(
        MCRYPT_RIJNDAEL_128,
        $key,
        $decrypted . md5($decrypted),
        MCRYPT_MODE_CBC,
        $initVector
      )
    );
    return $iv_base64 . $encrypted;
  }

  /**
  * Decrypts any data that belongs to a set user
  * Example: $UserSystem->decrypt("4lj84mui4htwyi58g7gh5y8hvn8t", "bob")
  *
  * @access public
  * @param string $encrypted
  * @param int $identifier
  * @return string
  */
  public function decrypt ($encrypted, $identifier) {
    $salt = $this->dbSel(["users", ["id" => $identifier]]);
    if ($salt[0] == 0) return false;
    $key = hash('SHA256', $salt[1]["salt"], true);
    $initVector  = base64_decode(substr($encrypted, 0, 22) . '==');
    $encrypted = substr($encrypted, 22);
    $decrypted = rtrim(
      mcrypt_decrypt(
        MCRYPT_RIJNDAEL_128,
        $key,
        base64_decode($encrypted),
        MCRYPT_MODE_CBC,
        $initVector
      ),
      "\0\4"
    );
    $hash = substr($decrypted, -32);
    $decrypted = substr($decrypted, 0, -32);
    if (md5($decrypted) != $hash) return false;
    return $decrypted;
  }

  /**
  * Inserts a user blob into the database for you
  * Example: $UserSystem->insertUserBlob("bob", "twoStep")
  *
  * @access public
  * @param int $identifier
  * @param mixed $action
  * @return boolean
  */
  public function insertUserBlob ($identifier, $action = "session") {
    $salt = $this->dbSel(["users", ["id" => $identifier]]);
    if ($salt[0] == 0) return false;
    $hash = $this->createSalt();
    $hash = $hash.md5($salt[1]["salt"].$hash);
    $this->dbIns(
      [
        "userblobs",
        [
          "user" => $identifier,
          "code" => $hash,
          "action" => $action,
          "date" => time()
        ]
      ]
    );
    return $hash;
  }

  /**
   * Checks if a user is banned
   * Example: $UserSystem->checkBan("bob")
   *
   * @access public
   * @param mixed $identifier
   * @return boolean
   */
  public function checkBan ($identifier = false) {
    $ipAddress = $this->getIP();
    if (ENCRYPTION === true) $ipAddress = encrypt($ipAddress, $identifier);

    $thing = false;

    $stmt = $this->dbSel(["ban", ["ip" => $ipAddress]]);
    $rows = $stmt[0];
    unset($stmt[0]);
    if ($rows > 0)
      foreach ($stmt as $ban)
        if ($ban['appealed'] === 0)
          if ($thing === false || (is_numeric($thing) && $ban["date"]>$thing))
            $thing = $ban["date"];

    if ($identifier !== false) {
      $identifier = $this->sanitize($identifier, "n");
      $user = $this->dbSel(["users", ["id" => $identifier]])[0];
      if ($user != 1) return "user";
      $stmt = $this->dbSel(["ban", ["user" => $identifier]]);
      $rows = $stmt[0];
      unset($stmt[0]);
      if ($rows > 0)
        foreach ($stmt as $ban)
          if ($thing === false || (is_numeric($thing) && $ban["date"]>$thing))
            return true;
    }

    return is_numeric($thing) ? true : false;
  }

  /**
  * Allows a user to send a link to reset their passsword if they forgot it.
  * Example: $UserSystem->sendRecover("example@pie.com")
  *
  * @access public
  * @param string $email
  * @return mixed
  */
  public function sendRecover ($email) {
    $email = $this->sanitize($email, "e");
    $select = $this->dbSel(["users", ["email"=>$email]]);
    if ($select[0] == 1) {
      $blob = $this->insertUserBlob($select[1]["id"], "recover");
      $link = $this->sanitize(
        URL_PREFACE."://".DOMAIN."/".RECOVERY_PG."?blob=$blob",
        "u"
      );
      $this->sendMail(
        $email,
        "Recover your ".SITENAME." account",
        "        Hello ".$select[1]["username"]."

        To reset your password click the link below.
        $link

        ======

        If this wasn't you,you should update your password on ".SITENAME.".

        Thank you"
      );
      return true;
    }
    return "email";
  }
}
