<?php

class browse extends utilities {

  /**
   * Method to return a list of lobbies available for joining
   * @return lobby[]
   * @throws Exception for DateTime
   */
  public function browseLobbies(): array {
    $currentTimestamp = (new DateTime())->getTimestamp();
    $max_age = $currentTimestamp - 60 * 60 * BROWSER_LOBBY_AGE_RESTRICTION;

    $lobbies = $this->DATABASE->selectAll(
      'gameLobbies',
      [ 'date' => [ '>', $max_age] ],
      null,
      [ 'name' => 'asc']
    );

    foreach ($lobbies as &$lobby) {
      $lobby = new lobby($lobby);
      $lobby->setOwnerName($this);
    }

    return $lobbies;
  }

}