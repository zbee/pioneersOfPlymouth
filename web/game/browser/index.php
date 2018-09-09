<?php
require_once '/var/www/pop/web/assets/autoload.php';

if (!$isLoggedIn)
  $UserSystem->redirect301('/user/login?mustBeLoggedIn');

$currentTimestamp = (new DateTime())->getTimestamp();
$ageRestriction = $currentTimestamp - (60 * 60 * 2);

$games = $UserSystem->dbSel(
  [
    'gameLobbies',
    ['date' => ['>', $ageRestriction]],
    ['name', 'asc']
  ]
);

echo '<div class="ribbon"><div><table><tr>';
echo '<th></th>';
echo '<th>Owner</th>';
echo '<th>Hosted By</th>';
echo '<th>Invite Only</th>';
echo '<th>Max Players</th>';
echo '<th>Language</th>';
echo '</tr>';

foreach ($games as $key => $game) {
  if (!is_array($game))
    continue;

  $ownerLoad = $UserSystem->dbSel(
    ['users', ['id' => $game['owner']]]
  )[1];
  $owner = $ownerLoad['username'];

  $inviteOnly = $game['inviteOnly'] == 1 ? '&#128505;' : '&#9744;';

  $language = Locale::getDisplayLanguage($game['language'], 'en');

  $row = "<tr class='clickable-row' data-href='/game/$game[id]'>";
  $row .= "<td>$game[name]</td>";
  $row .= "<td>$owner</td>";
  $row .= "<td>$inviteOnly</td>";
  $row .= "<td>$game[maxPlayers]</td>";
  $row .= "<td>$game[expansionsIncluded]</td>";
  $row .= "<td>$language</td>";
  $row .= "</tr>";
  echo $row;
  echo $row;
}

echo '</table></div>';
?>

<script>
  jQuery(document).ready(function ($) {
    $(".clickable-row").click(function () {
      window.location = $(this).data("href");
    });
  });
</script>
