<?php

use DBAL\Database;
use DBAL\Caching\MemcachedCache;

class databaseAbstraction {

  public Database $DATABASE;
  public MemcachedCache $caching;

  public function __construct() {
    try {
      $this->DATABASE = new DBAL\Database(
        DB_LOCATION, DB_USERNAME, DB_PASSWORD, DB_DATABASE
      );
      $this->DATABASE->setCaching($this->caching);
    } catch (Exception $ex) {
      $pdo = $ex;
    }

    if (!is_object($this->DATABASE) || isset($pdo)) {
      throw new Exception (
        "DB_* constants in config.inc failed to connect to a database. " . $pdo
      );
    }
  }

}