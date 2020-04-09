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
    $this->id             = (string)$lobby['id'];
    $this->uuid           = $this->getUUID((string)$lobby['id']);

    if (array_key_exists('ownerID', $lobby)) {
      $this->owner     = (string)$lobby['owner'];
      $this->ownerUUID = $this->getUUID((string)$lobby['ownerID']);
    } else {
      $this->ownerUUID = $this->getUUID((string)$lobby['owner']);
    }

    $this->languageStub   = (string)$lobby['language'];
    $this->language       = Locale::getDisplayLanguage(
      $lobby['language'],
      $this->languageStub
    );
    $this->name           = (string)$lobby['name'];
    $this->inviteOnly     = (bool)$lobby['inviteOnly'];
    $this->inviteOnlyText = $this->inviteOnly ? 'Yes' : 'No';
    $this->maxPlayers     = (int)$lobby['maxPlayers'];
    $dateCreated          = new DateTime();
    $dateCreated          = $dateCreated->setTimestamp((int)$lobby['date']);
    $this->dateCreated    = $dateCreated;
    $this->expansionsStub = (int)$lobby['expansionsIncluded'];
  }
}