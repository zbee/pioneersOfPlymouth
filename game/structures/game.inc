<?php

class game extends pioneersOfPlymouth {
  public int            $id;
  private string        $languageStub;
  public string         $language;
  public string         $owner;
  public string         $ownerUUID;
  public string         $name;
  public bool           $inviteOnly;
  public string         $inviteOnlyText;
  public int            $maxPlayers;
  public DateTime       $dateCreated;
  private int           $expansionsStub;
  public array          $expansionsAvailable;
  public string         $expansionsListed;

  public function __construct(array $game) {
    $this->id             = (int)$game['id'];
    $this->owner          = (string)$game['owner'];
    $this->ownerUUID      = $this->getUUID((string)$game['ownerID']);
    $this->languageStub   = (string)$game['language'];
    $this->language       = Locale::getDisplayLanguage(
      $game['language'],
      $this->languageStub
    );
    $this->name           = (string)$game['name'];
    $this->inviteOnly     = (bool)$game['inviteOnly'];
    $this->inviteOnlyText = $this->inviteOnly ? 'Yes' : 'No';
    $this->maxPlayers     = (int)$game['maxPlayers'];
    $dateCreated          = new DateTime();
    $dateCreated          = $dateCreated->setTimestamp((int)$game['date']);
    $this->dateCreated    = $dateCreated;
    $this->expansionsStub = (int)$game['expansionsIncluded'];
  }
}