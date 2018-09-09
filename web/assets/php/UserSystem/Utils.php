<?php
/**
* Class full of utility methods for the UserSystem class.
*
* @package    UserSystem
* @author     Ethan Henderson <ethan@zbee.me>
* @copyright  Copyright 2014-2015 Ethan Henderson
* @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link       https://github.com/zbee/usersystem
* @since      Class available since Release 0.48
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

class Utils {

  var $DATABASE = "";

  /**
  * Initializes the class and connects to the database.
  * Example: $UserSystem = new UserSystem ("")
  *
  * @access public
  * @param string $dbname
  * @return void
  */
  public function __construct ($dbname = DB_DATABASE) {
    try {
      $this->DATABASE = new PDO(
        "mysql:host=".DB_LOCATION.";dbname=".$dbname.";charset=utf8",
        DB_USERNAME,
        DB_PASSWORD
      );
    } catch(PDOException $ex) {
      $pdo = $ex;
    }

    if (!is_object($this->DATABASE) || isset($pdo)) {
      throw new Exception (
        "DB_* constants in config.php failed to connect to a database. " . $pdo
      );
    }
  }

  /**
  * Gets the IP of the current user
  * Example: $UserSystem->getIP()
  *
  * @access public
  * @return mixed
  */
  public function getIP () {
    $srcs = [
      'HTTP_CLIENT_IP',
      'HTTP_X_FORWARDED_FOR',
      'HTTP_X_FORWARDED',
      'HTTP_X_CLUSTER_CLIENT_IP',
      'HTTP_FORWARDED_FOR',
      'HTTP_FORWARDED',
      'REMOTE_ADDR'
    ];
    foreach ($srcs as $key)
      if (array_key_exists($key, $_SERVER) === true)
        foreach (explode(',', $_SERVER[$key]) as $ip)
          if (filter_var($ip, FILTER_VALIDATE_IP) !== false) return $ip;
    return false;
  }

  /**
  * Gives the current url that the user is on.
  * Example: $UserSystem->currentURL()
  *
  * @access public
  * @return string
  */
  public function currentURL () {
    $domain = htmlspecialchars(
      $_SERVER['HTTP_HOST'],
      ENT_QUOTES,
      "utf-8"
    );
    $page = htmlspecialchars(
      $_SERVER['REQUEST_URI'],
      ENT_QUOTES,
      "utf-8"
    );
    $http = isset($_SERVER['HTTPS']) ? "https" : "http";
    return "$http://$domain$page";
  }

  /**
  * Provides the proper headers to redirect a user, including a page-has-moved
  * flag.
  * Example: $UserSystem->redirect301("http://example.com")
  *
  * @access public
  * @param string $url
  * @return boolean
  */
  public function redirect301($url) {
    if (!headers_sent()) {
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: $url");
      return true;
    }
    return false;
  }

  /**
  * Generates a more secure random number
  * Example: $UserSystem->openssl_rand(0, 100)
  *
  * @access public
  * @param int min
  * @param int max
  * @return int
  */
  function opensslRand($min = 0, $max = 1000) {
    $range = $max - $min;
    if ($range < 1) return $min;
    $log = log($range, 2);
    $bytes = (int) ($log / 8) + 1;
    $bits = (int) $log + 1;
    $filter = (int) (1 << $bits) - 1;
    do {
      $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
      $rnd = $rnd & $filter;
    } while ($rnd >= $range);
    return $min + $rnd;
  }

  /**
  * Generates a new salt based off of a username
  * Example: $UserSystem->createSalt("Bob")
  *
  * @access public
  * @return string
  */
  public function createSalt () {
    return hash(
      "sha512",
      time()*sqrt(strlen(DOMAIN))
      . ($str = substr(
        str_shuffle(
          str_repeat(
            "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
            . "`~0123456789!@$%^&*()-_+={}[]\\|:;'\"<,>."
            . bin2hex(openssl_random_pseudo_bytes(64)),
            $this->opensslRand(32, 64+strlen(SITENAME))
          )
        ),
        1,
        $this->opensslRand(2048, 8192)
      ))
      . ($strt = bin2hex(openssl_random_pseudo_bytes(strlen($str)/8)))
      . strlen($strt)*$this->opensslRand(4, 128)
    );
  }

  /**
  * Makes any string safe for HTML by converting it to an entity code.
  * Example: $UserSystem->handleUTF8("g'°")
  *
  * @access public
  * @param string $code
  * @return string
  */
  public function handleUTF8 ($code) {
    return preg_replace_callback('/[\x{80}-\x{10FFFF}]/u', function($match) {
      list($utf8) = $match;
      return mb_convert_encoding($utf8, 'HTML-ENTITIES', 'UTF-8');
    },
    $code);
  }

  /**
  * $this->sanitizes any given string in a particular fashion of your choosing.
  * Example: $UserSystem->sanitize("dirt")
  *
  * @access public
  * @param string $data
  * @param string $type
  * @return mixed
  */
  public function sanitize ($data, $type = "s") {
    if ($type == "n") {
      $data = filter_var(trim($data), FILTER_SANITIZE_NUMBER_FLOAT);
      $data = preg_replace("/[^0-9]/", "", $data);
      return intval($data);
    } elseif ($type == "s") {
      $data = $this->handleUTF8($data);
      $data = filter_var($data, FILTER_SANITIZE_STRING);
      return filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    } elseif ($type == "d") {
      $data = preg_replace("/[^0-9\-\s\+a-zA-Z]/", "", trim($data));
      if (is_numeric($data) !== true) $data = strtotime($data);
      $month = date("n", $data);
      $day = date("j", $data);
      $year = date("Y", $data);
      if (checkdate($month, $day, $year)) return $data;
    } elseif ($type == "h") {
      return $this->handleUTF8(trim($data));
    } elseif ($type == "q") {
      $data = htmlentities($this->handleUTF8($data));
      return $data;
    } elseif ($type == "b") {
      if ($data === true || $data === false) return $data;
    } elseif ($type == "u") {
      if (
        filter_var(
          filter_var(
            $data,
            FILTER_SANITIZE_URL
          ),
          FILTER_VALIDATE_URL
        )
        ===
        $data
      ) return $data;
    } elseif ($type == "i") {
      if (filter_var($data, FILTER_VALIDATE_IP) !== false) return $data;
    } elseif ($type == "e") {
      if (
        filter_var(
          filter_var(
            $data,
            FILTER_SANITIZE_EMAIL
          ),
          FILTER_VALIDATE_EMAIL
        )
        ===
        $data
      ) return $data;
    }

    return "FAILED";
  }


  /**
  * Sends properly formatted emails out from the system to many or just one user
  * Example: $UserSystem->sendMail(["bob@ex.com", "rob@ex.com"], "Hi", "Hello!")
  *
  * @access public
  * @param mixed recipient
  * @param string subject
  * @param string message
  * @return bool
  */
  public function sendMail ($recipient, $subject, $message) {
    $recipients = "";
    if (is_array($recipient)) {
      foreach ($recipient as $r) $recipients .= $this->sanitize($r, "e") . ", ";
    } else {
      $recipients = $this->sanitize($recipient, "e");
    }
    $recipient = $this->sanitize($recipients, "s");
    $subject = $this->sanitize($subject, "s");
    $headers = 'From: noreply@'.DOMAIN."\n" .
    'Reply-To: support@'.DOMAIN."\n" .
    'X-Mailer: PHP/'.phpversion();
    return mail($recipient, $subject, $message, $headers);
  }
}
