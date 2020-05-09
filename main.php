<?php
require 'vendor/autoload.php';
require 'data.php';
require 'Faction.php';
require 'Player.php';

ob_start();

$time_start = microtime(true); // Time how long it takes to run

echo "
    <style>
    table, th, td {
        border: 1px solid black;
    }
    </style>";

set_time_limit(1200); //Set limit for execution, this does not include sleep time!

$data = new Data();
$playerIds = [];

foreach($data->getFactionList() as $id) {
    $faction = new Faction($data, $id);
    foreach ($faction->getPlayersInFaction()->members as $key => $member) {
        $playerIds[] = $key;
    }
}

$i = 0;
$playersWithCompanies = [];
foreach($playerIds as $id) {
    $player = new Player($data, $id);
    if($player->isCompanyOwner())
        $playersWithCompanies[] = $player;
    $i++;
    usleep(800 * 1000); // 800ms
//    if($i >= 20) break;
}

echo "<table style=\"width:100%\">";
echo "<thead>
        <tr>
            <th>Player Name</th>
            <th>Company Name</th>
            <th>Type</th>
            <th>Rank</th>
            <th>Positions</th>
        </tr></thead><tbody>";

foreach($playersWithCompanies as $player) {
    echo "<tr>";

    $company = $player->getCompany();

    echo "<td>" . $player->getPlayerNameToHtml() . '</td>';
    echo "<td>" . $player->getPlayerCompanyNameToHtml() . '</td>';
    echo "<td>" . $company->getCompanyType() . '</td>';
    echo "<td>" . $company->getCompanyStars() . '</td>';
    echo "<td>" . $company->getCompanyPositionsFilled() . "/" . $company->getCompanyPositionsMax() . '</td>';

    echo "</tr>";
    usleep(800 * 1000); // 500ms
}
echo "</tbody></table>";

$time_end = microtime(true);

$execution_time = ($time_end - $time_start)/60;
echo '<b>Total Execution Time:</b> '.number_format((float) $execution_time, 10) .' Minutes<br>';
echo "<b>Players Checked: <b>" . $i . "<br>";
echo "<b>Last Generated: </b>" . date("Y-m-d H:I") . " EST" . "<br>";
file_put_contents("index.html", ob_get_contents());
ob_end_clean();