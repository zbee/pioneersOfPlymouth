<?php

class game extends pioneersOfPlymouth {
  private int $id;
  private string $language_stub;
  public string $language;
  private string $owner;
  private string $gameName;
  public bool $inviteOnly;
  public int $maxPlayers;
  public int $date_created;
  private int $expansions_stub;
  public array $expansions_available;
  public string $expansions_listed;

  public function __construct(int $id) {

  }
}