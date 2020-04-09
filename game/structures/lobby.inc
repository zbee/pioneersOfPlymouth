<?php

class lobby extends pioneersOfPlymouth {
  private string             $id;
  public string              $uuid;
  private string             $languageStub;
  public string              $language;
  public ?string             $owner;
  public string              $ownerUUID;
  public string              $name;
  public bool                $inviteOnly;
  public string              $inviteOnlyText;
  public int                 $maxPlayers;
  public DateTime            $dateCreated;
  private int                $expansionsStub;
  public array               $expansionsAvailable;
  public string              $expansionsListed;

  public function __construct(array $lobby) {
    $this->id        = (string)$lobby['lobbyID'];
    $this->uuid      = $this->getUUID((string)$lobby['lobbyID']);
    $this->ownerUUID = $this->getUUID((string)$lobby['owner']);

    $this->languageStub   = (string)$lobby['language'];
    $this->language       = Locale::getDisplayLanguage(
      $lobby['language'],
      $this->languageStub
    );

    $this->name           = (string)$lobby['name'];
    $this->maxPlayers     = (int)$lobby['maxPlayers'];

    $this->inviteOnly     = (bool)$lobby['inviteOnly'];
    $this->inviteOnlyText = $this->inviteOnly ? 'Yes' : 'No';

    $dateCreated          = new DateTime();
    $dateCreated          = $dateCreated->setTimestamp((int)$lobby['date']);
    $this->dateCreated    = $dateCreated;

    $this->expansionsStub = (int)$lobby['expansionsIncluded'];
  }

  public function setOwnerName(pioneersOfPlymouth $pop): void {
    $binaryID    = $this->getBinary($this->ownerUUID);
    $this->owner = $pop->DATABASE->select(
      'users',
      ['id' => $binaryID]
    )['username'];
  }
}