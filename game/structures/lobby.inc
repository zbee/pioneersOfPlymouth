<?php

class lobby extends pioneersOfPlymouth {
  private string   $id;
  public string    $uuid;
  private string   $languageStub;
  public string    $language;
  public ?user     $owner;
  public string    $ownerUUID;
  public string    $name;
  public bool      $inviteOnly;
  public string    $inviteOnlyText;
  public int       $maxPlayers;
  public DateTime  $dateCreated;
  private int      $expansionsStub;
  public array     $expansionsAvailable;
  public string    $expansionsListed;
  public string    $joinURL;
  public string    $lobbyURL;
  /**
   * @var null|user[] List of players connected to lobby
   */
  public ?array    $players;

  public function __construct(array $lobby) {
    $this->id        = (string)$lobby['lobbyID'];
    $this->uuid      = $this->getUUID((string)$lobby['lobbyID']);
    $this->ownerUUID = $this->getUUID((string)$lobby['owner']);
    $this->joinURL   = URL_SCHEME_JOIN_LOBBY . $this->uuid;
    $this->lobbyURL  = URL_SCHEME_LOBBY . $this->uuid;

    $this->languageStub = (string)$lobby['language'];
    $this->language     = Locale::getDisplayLanguage(
      $lobby['language'],
      $this->languageStub
    );

    $this->name       = (string)$lobby['name'];
    $this->maxPlayers = (int)$lobby['maxPlayers'];

    $this->inviteOnly     = (bool)$lobby['inviteOnly'];
    $this->inviteOnlyText = $this->inviteOnly ? 'Yes' : 'No';

    $dateCreated       = new DateTime();
    $dateCreated       = $dateCreated->setTimestamp((int)$lobby['date']);
    $this->dateCreated = $dateCreated;

    $this->expansionsStub = (int)$lobby['expansionsIncluded'];
  }

  /**
   * Method to load and set the owner's user object
   *
   * @param pioneersOfPlymouth|load|browse|object $pop the pioneersOfPlymouth
   *                                                   object
   */
  public function loadOwner(object $pop): void {
    $binaryID    = $this->getBinary($this->ownerUUID);
    $this->owner = new user($binaryID, $pop);
  }

  /**
   * Method to load and set each player
   *
   * @param pioneersOfPlymouth|load|browse|object $pop the pioneersOfPlymouth
   *                                                   object
   *
   * @throws Exception for DateTime
   */
  public function loadPlayers(object $pop): void {
    $currentTimestamp = (new DateTime())->getTimestamp();
    $maxTilKill       = CONNECTION_MAX_AGE_TIL_KILL * 60;
    $maxAge           = $currentTimestamp - $maxTilKill;
    $playersConnected = $pop->DATABASE->selectAll(
      'connections',
      [
        'connectionType' => CONNECTION_TYPE_LOBBY,
        'connectionID'   => $this->id,
        'lastPing'       => ['>=', $maxAge],
      ],
      ['userID'],
      ['lastPing' => 'desc']
    );

    if ($playersConnected == false) {
      $this->killLobby($pop);
      return;
    }

    $this->players    = [];

    foreach ($playersConnected as $player) {
      if (count($this->players) >= $this->maxPlayers)
        continue;
      $this->players[] = new user($player['userID'], $pop);
    }
  }

  /**
   * Method to remove a lobby
   *
   * @param pioneersOfPlymouth|load|browse|object $pop the pioneersOfPlymouth
   *                                                   object
   */
  private function killLobby(object $pop): void {
  }
}