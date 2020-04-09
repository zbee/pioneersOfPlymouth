<?php

class load extends browse {
  private lobby $lobby;
  public bool  $lobbyLoaded;

  public function loadLobby(string $lobbyUUID): void {
    $lobbyID = $this->getBinary($lobbyUUID);

    $lobby = $this->DATABASE->select('gameLobbies', ['lobbyID' => $lobbyID]);

    if (!is_array($lobby))
      $this->lobbyLoaded = false;
    else {
      $this->lobby       = new lobby($lobby);
      $this->lobby->setOwnerName($this);
      $this->lobbyLoaded = true;
    }
  }

  public function getLobby(): lobby {
    return $this->lobby;
  }
}