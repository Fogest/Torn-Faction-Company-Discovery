<?php
require 'vendor/autoload.php';

use Noodlehaus\Config;

class Data
{
    private $factionList;
    private $tornApiBase;
    private $tornApiKey;

    public function __construct()
    {
        $conf = new Config('config.json');
        $this->factionList = $conf['factionsToParse'];
        $this->tornApiBase = $conf['tornApiBase'];
        $this->tornApiKey = $conf['apiKey'];
    }

    /**
     * @return mixed
     */
    public function getFactionList()
    {
        return $this->factionList;
    }

    /**
     * @return mixed
     */
    public function getTornApiBase()
    {
        return $this->tornApiBase;
    }

    /**
     * @return mixed
     */
    public function getTornApiKey()
    {
        return $this->tornApiKey;
    }

}