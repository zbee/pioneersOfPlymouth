<?php

class browse extends pioneersOfPlymouth {

  public array $browser;

  public function __construct(pioneersOfPlymouth $pop) {
    $currentTimestamp = (new DateTime())->getTimestamp();
    $max_age = $currentTimestamp - 60 * 60 * BROWSER_AGE_RESTRICTION;

    $this->browser = $pop->DATABASE->selectAll(
      'gameLobbies',
      [ 'date' => [ '>', $max_age] ],
      null,
      [ 'name' => 'asc']
    );
  }

}