<?php

class utilities extends databaseAbstraction {

  /**
   * @var string $userID binary of user id
   */
  public string $userID;
  /**
   * @var string $userUUID UUID of user id
   */
  public string $userUUID;

  public function getUUID(string $binary): string {
    return preg_replace(
      "/(\w{8})(\w{4})(\w{4})(\w{4})(\w{12})/i",
      "$1-$2-$3-$4-$5",
      bin2hex($binary)
    );
  }

  public function getBinary(string $uuid): string {
    return hex2bin(
      str_replace('-', '', $uuid)
    );
  }

  /**
   * @param string $userID Binary of user ID
   */
  public function setUser(string $userID): void {
    $this->userID   = $userID;
    $this->userUUID = $this->getUUID($userID);
  }

  public function redirect(string $url): bool {
    if (!headers_sent()) {
      header("Location: $url");
      return true;
    }
    return false;
  }

}