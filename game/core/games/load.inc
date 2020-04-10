<?php

class load extends browse {
  /**
   * @var string $lobbyID Binary lobby identifier
   */
  private string $lobbyID;
  /**
   * @var string $lobbyUUID UUID lobby identifier
   */
  private string $lobbyUUID;
  private lobby  $lobby;
  public bool    $lobbyLoaded;

  public function loadLobby(string $lobbyUUID): void {
    $this->lobbyID   = $this->getBinary($lobbyUUID);
    $this->lobbyUUID = $lobbyUUID;

    if (!$this->inLobby())
      $this->joinLobby();

    $lobby = $this->DATABASE->select(
      'gameLobbies',
      ['lobbyID' => $this->lobbyID]
    );

    if (!is_array($lobby))
      $this->lobbyLoaded = false;
    else {
      $this->lobby = new lobby($lobby);
      $this->lobby->setOwnerName($this);
      $this->lobbyLoaded = true;
    }
  }

  private function joinLobby(): void {
    $currentTimestamp = (new DateTime())->getTimestamp();
    $this->DATABASE->insert(
      'connections',
      [
        'userID'         => $this->userID,
        'connectionType' => CONNECTION_TYPE_LOBBY,
        'connectionID'   => $this->lobbyID,
        'lastPing'       => $currentTimestamp,
      ]
    );
    $this->redirect(URL_SCHEME_LOBBY . $this->lobbyUUID);
  }

  private function inLobby(string $user = 'checkClass'): bool {
    if ($user == 'checkClass')
      $user = $this->userID;

    $checkLobbyStatus = $this->DATABASE->count(
      'connections',
      [
        'userID'         => $user,
        'connectionType' => CONNECTION_TYPE_LOBBY,
        'connectionID'   => $this->lobbyID,
      ]
    );

    if ((int)$checkLobbyStatus !== 1)
      return false;
    return true;
  }

  public function getLobby(): lobby {
    return $this->lobby;
  }
}