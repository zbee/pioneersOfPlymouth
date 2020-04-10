<?php

class user extends pioneersOfPlymouth {
  public string $id;
  public string $username;
  public string $uuid;
  public string $avatar;
  public string $avatarLarge;
  public string $preferredColor;

  public function __construct(string $userID, object $pop) {
    $this->id       = $userID;
    $this->uuid     = $this->getUUID($this->id);
    $this->username = $pop->DATABASE->select(
      'users',
      ['id' => $this->id]
    )['username'];

  }

}