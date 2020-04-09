<?php

class load extends browse {
  public lobby $lobby;
  public bool  $lobbyLoaded;

  public function loadLobby(string $lobbyUUID): void {
    $lobbyID = $this->getBinary($lobbyUUID);

    $lobby = $this->DATABASE->select('gameLobbies', ['id' => $lobbyID]);

    if (!is_array($lobby))
      $this->lobbyLoaded = false;
    else {
      $this->lobby       = new lobby($lobby);
      $this->lobbyLoaded = true;
    }
  }
}