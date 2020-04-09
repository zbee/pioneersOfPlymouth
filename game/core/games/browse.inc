<?php

class browse extends utilities {

  public function browseLobbies() {
    $currentTimestamp = (new DateTime())->getTimestamp();
    $max_age = $currentTimestamp - 60 * 60 * BROWSER_LOBBY_AGE_RESTRICTION;

    $games = $this->DATABASE->selectAll(
      'gameLobbies',
      [ 'date' => [ '>', $max_age] ],
      null,
      [ 'name' => 'asc']
    );

    foreach ($games as &$game) {
      $game['ownerID'] = $game['owner'];
      $game['owner'] = $this->DATABASE->select(
        'users', ['id' => $game['owner']], [ 'username' ]
      )['username'];
      $game = new lobby($game);
    }

    return $games;
  }

}