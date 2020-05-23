<?php
require 'vendor/autoload.php';
require 'data.php';
require 'Faction.php';
require 'Player.php';

function isLocalhost($whitelist = ['127.0.0.1', '::1']) {
    return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
}
function isCommandLineInterface()
{
    return (php_sapi_name() === 'cli');
}

ob_start();

$time_start = microtime(true); // Time how long it takes to run

echo "</head><body>";

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
    if (isLocalhost()) usleep(50 * 1000); // 50ms only as less iterations are run locally
    else usleep(800 * 1000); // 800ms
    $i++;
    if($i >= 30 && isLocalhost()) break;
}

echo "<table id='directory-table' class='display'>";
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
    if (isLocalhost()) usleep(50 * 1000); // 50ms only as less iterations are run locally
    else usleep(800 * 1000); // 800ms
}
echo "</tbody></table>";

$time_end = microtime(true);

$execution_time = ($time_end - $time_start)/60;
echo '<b>Total Execution Time:</b> '.number_format((float) $execution_time, 10) .' Minutes<br>';
echo "<b>Players Checked: </b>" . $i . "<br>";
echo "<b>Last Generated: </b>" . date("Y-m-d H:i") . " EST" . "<br>";
if(isLocalhost()) echo "<b><a href='main.php'>Force Generate</a></b><br>";

echo "</body>
</html>";

$header = file_get_contents("head.html");
$pageContents = $header . ob_get_contents();
file_put_contents("index.html", $pageContents);
ob_end_clean();

if(!isCommandLineInterface()) {
    header("Location: index.html");
    die();
}