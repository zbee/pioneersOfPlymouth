<?php

use DBAL\Database;
use DBAL\Caching\MemcacheCache;

class databaseAbstraction {

  public Database $DATABASE;
  public MemcacheCache $caching;

  public function __construct() {
    try {
      $this->DATABASE = new Database(
        DB_LOCATION, DB_USERNAME, DB_PASSWORD, DB_DATABASE
      );
      $this->caching = new MemcacheCache();
      $this->caching->addServer('127.0.0.1', 11211);
      $this->DATABASE->setCaching($this->caching);
    } catch (Exception $ex) {
      $pdo = $ex;
      var_dump($ex);
    }

    if (!is_object($this->DATABASE) || isset($pdo)) {
      throw new Exception (
        "DB_* constants in config.inc failed to connect to a database. " . $pdo
      );
    }
  }

}