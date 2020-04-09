<?php

class utilities extends databaseAbstraction {

  public function getUUID (string $binary) {
    return preg_replace(
      "/(\w{8})(\w{4})(\w{4})(\w{4})(\w{12})/i",
      "$1-$2-$3-$4-$5",
      bin2hex($binary)
    );
  }

  public function getBinary (string $uuid) {
    return hex2bin(
      str_replace('-', '', $uuid)
    );
  }

}