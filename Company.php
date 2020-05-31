<?php
require __DIR__ . '/vendor/autoload.php';

class Company
{

    private $client;
    private $data;
    private $companyId;

    private $companyName;
    private $companyType;
    private $companyTypeId;
    private $companyStars;
    private $companyPositionsMax;
    private $companyPositionsFilled;

    public function __construct($data, $companyId)
    {
        $this->data = $data;
        $this->companyId = $companyId;
        $this->client = new GuzzleHttp\Client([
            'verify' => false,
            'base_uri' => $this->data->getTornApiBase(),
            'timeout' => 2.0
        ]);

        $this->getCompanyData();
    }

    private function getCompanyData() {
        $res = $this->client->get("company/" . $this->companyId . "?selections=profile&key=" . $this->data->getTornApiKey());

        $company = json_decode($res->getBody());
        $this->companyName = $company->company->name;
        $this->companyTypeId = $company->company->company_type;
        $this->companyType = $this->getPrettyType($company->company->company_type);
        $this->companyStars = $company->company->rating;
        $this->companyPositionsFilled = $company->company->employees_hired;
        $this->companyPositionsMax = $company->company->employees_capacity;
    }

    /**
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @return mixed
     */
    public function getCompanyType()
    {
        return $this->companyType;
    }

    /**
     * @return mixed
     */
    public function getCompanyTypeId()
    {
        return $this->companyTypeId;
    }

    /**
     * @return mixed
     */
    public function getCompanyStars()
    {
        return $this->companyStars;
    }

    /**
     * @return mixed
     */
    public function getCompanyPositionsMax()
    {
        return $this->companyPositionsMax;
    }

    /**
     * @return mixed
     */
    public function getCompanyPositionsFilled()
    {
        return $this->companyPositionsFilled;
    }

    private function getPrettyType($type) {
        $i = (int) $type;
        switch ($i) {
            case 19:
                return "Firework Stand";
            case 3:
                return "Flower Shop";
            case 8:
                return "Candle Shop";
            case 20:
                return "Property Broker";
            case 1:
                return "Hair Salon";
            case 5:
                return "Clothing Store";
            case 27:
                return "Restaurant";
            case 14:
                return "Sweet Shop";
            case 25:
                return "Pub";
            case 7:
                return "Game Shop";
            case 23:
                return "Music Store";
            case 10:
                return "Adult Novelties";
            case 32:
                return "Lingerie Store";
            case 12:
                return "Grocery Store";
            case 9:
                return "Toy Shop";
            case 21:
                return "Furniture Store";
            case 6:
                return "Gun Shop";
            case 30:
                return "Mechanic Shop";
            case 11:
                return "Cyber Cafe";
            case 33:
                return "Meat Warehouse";
            case 2:
                return "Law Firm";
            case 26:
                return "Gents Strip Club";
            case 36:
                return "Ladies Strip Club";
            case 34:
                return "Farm";
            case 4:
                return "Car Dealership";
            case 35:
                return "Software Corporation";
            case 24:
                return "Nightclub";
            case 39:
                return "Detective Agency";
            case 29:
                return "Fitness Center";
            case 22:
                return "Gas Station";
            case 13:
                return "Theater";
            case 31:
                return "Amusement Park";
            case 18:
                return "Zoo";
            case 15:
                return "Cruise Line";
            case 37:
                return "Private Security Firm";
            case 40:
                return "Logistics Management";
            case 38:
                return "Mining Corporation";
            case 16:
                return "Television Network";
            case 28:
                return "Oil Rig";
            default:
                return "Unknown";
        }
    }
}