<?php

class browse extends utilities {

  /**
   * Method to return a list of lobbies available for joining
   * @return lobby[]
   * @throws Exception for DateTime
   */
  public function browseLobbies(): array {
    $currentTimestamp = (new DateTime())->getTimestamp();
    $age_restriction  = 60 * 60 * BROWSER_LOBBY_AGE_RESTRICTION;
    $max_age          = $currentTimestamp - $age_restriction;

    $lobbies = $this->DATABASE->selectAll(
      'gameLobbies',
      ['date' => ['>', $max_age]],
      null,
      ['name' => 'asc']
    );

    foreach ($lobbies as &$lobby) {
      $lobby = new lobby($lobby);
      $lobby->loadOwner($this);
      $lobby->loadPlayers($this);
    }

    return $lobbies;
  }

}