<?php
require __DIR__ . '/vendor/autoload.php';
require 'Company.php';

class Player
{
    private $client;
    private $playerId;
    private $data;

    private $playerName;
    private $company;

    public function __construct($data, $playerId)
    {
        $this->data = $data;
        $this->playerId = $playerId;
        $this->client = new GuzzleHttp\Client([
            'verify' => false,
            'base_uri' => $this->data->getTornApiBase(),
            'timeout' => 2.0
        ]);
    }

    public function isCompanyOwner() {
        $res = $this->client->get("user/" . $this->playerId . "?selections=profile&key=" . $this->data->getTornApiKey());

        $player = json_decode($res->getBody());
        if ($player->job->position === "Director") {
            $this->playerName = $player->name;
            $this->company = new Company($this->data, $player->job->company_id);
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @return mixed
     */
    public function getPlayerName()
    {
        return $this->playerName;
    }

    public function getPlayerNameToHtml()
    {
        return "<a href='https://www.torn.com/profiles.php?XID=" . $this->playerId . "'>" .
            $this->playerName . "</a>";
    }

    public function getPlayerCompanyNameToHtml()
    {
        return "<a href='https://www.torn.com/joblist.php#/p=corpinfo&userID=" . $this->playerId . "'>" .
            $this->getCompany()->getCompanyName() . "</a>";
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }
}