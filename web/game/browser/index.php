<?php
require_once '/var/www/pop/web/assets/autoload.php';

if (!$isLoggedIn)
  $UserSystem->redirect('/user/login?mustBeLoggedIn');

$games = $pop->browseGames();

echo '<div class="ribbon"><div><table><tr>';
echo '<th></th>';
echo '<th>Owner</th>';
echo '<th>Invite Only</th>';
echo '<th>Max Players</th>';
echo '<th>Language</th>';
echo '</tr>';

/**
 * @var game $game
 */
foreach ($games as $game) {
  if (!is_object($game))
    continue;

  $row = "<tr class='clickable-row' data-href='/game/$game->id'>";
  $row .= "<td>$game->name</td>";
  $row .= "<td>$game->owner</td>";
  $row .= "<td>$game->inviteOnlyText</td>";
  $row .= "<td>$game->maxPlayers</td>";
  $row .= "<td>$game->language</td>";
  $row .= "</tr>";
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
