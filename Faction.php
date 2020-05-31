<?php
require __DIR__ . '/vendor/autoload.php';

class Faction
{
    private $client;
    private $factionId;
    private $data;
    public function __construct($data, $factionId)
    {
        $this->data = $data;
        $this->factionId = $factionId;
        $this->client = new GuzzleHttp\Client([
            'verify' => false,
            'base_uri' => $this->data->getTornApiBase(),
            'timeout' => 5.0
        ]);
    }

    public function getPlayersInFaction() {
        $res = $this->client->get("faction/" . $this->factionId . "?selections=basic&key=" . $this->data->getTornApiKey());
        return json_decode($res->getBody());
    }
}