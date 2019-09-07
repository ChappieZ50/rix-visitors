<?php

namespace App\Visitors;

use App\Config\Config;
use App\Database\Database;
use MongoDB\Client;

class RixVisitors implements RixVisitorsInterface
{
    public function getInfo($api = 'ipdata')
    {
        $data = $api === 'ipdata' ? $this->getIpDataInfo(Config::get('definitions.IP_DATA_KEY')) : $this->getGeolocationInfo();
        return [
            'ip'       => $this->getIP(),
            'browser'  => $this->getBrowser(),
            'referrer' => $this->getReferrer(),
            'current'  => Config::currentUrl(),
            'visit'    => 1,
            'info'     => $data,
        ];
    }

    public function getIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else
            $ip = $_SERVER['REMOTE_ADDR'];

        return $ip === '::1' ? 'localhost' : $ip;
    }

    public function getBrowser()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public function getReferrer()
    {
        $referrer = isset($_SERVER['HTTP_REFERER']) ? base64_encode($_SERVER['HTTP_REFERER']) : false;
        return empty($referrer) ? "Directly" : $referrer;
    }

    public function fetch($table, $driver = 'mongodb')
    {
        // Connecting to database
        try {
            $database = new Database($driver);
            $db = $database->connect();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        if ($driver === 'mongodb') {
            $ip = $this->getIP();
            if ($this->mongoVisitorExists($db, $table)) {
                // Updating counter
                return $database->table($table)->update(['$inc' => ['visit' => 1]], '', ['ip' => $ip, 'current' => Config::currentUrl()]);
            } else {
                // Adding new visitor
                return $database->table($table)->insert($this->getInfo());
            }
        }
    }

    public function getGeolocationInfo()
    {
        $geo = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $this->getIP());
        return [
            'status'        => (string)$geo->geoplugin_status,
            'request'       => (string)$geo->geoplugin_request,
            'delay'         => (string)$geo->geoplugin_delay,
            'city'          => (string)$geo->geoplugin_city,
            'regionCode'    => (string)$geo->geoplugin_regionCode,
            'continentCode' => (string)$geo->geoplugin_continentCode,
            'continentName' => (string)$geo->geoplugin_continentName,
            'countryName'   => (string)$geo->geoplugin_countryName,
            'timezone'      => (string)$geo->geoplugin_timezone,
            'currencyCode'  => (string)$geo->geoplugin_currencyCode,
        ];
    }

    public function getIpDataInfo($key)
    {
        $ch = curl_init('https://api.ipdata.co/159.146.4.212?api-key=' . $key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $ipData = json_decode($output);
        if (!isset($ipData->message))
            return [
                'status'        => 200,
                'city'          => (string)$ipData->city,
                'region'        => (string)$ipData->region,
                'regionCode'    => (string)$ipData->region_code,
                'countryName'   => (string)$ipData->country_name,
                'countryCode'   => (string)$ipData->country_code,
                'continentName' => (string)$ipData->continent_name,
                'continentCode' => (string)$ipData->continent_code,
                'timezone'      => $ipData->time_zone,
            ];
        else
            return [
                'status'  => 404,
                'message' => $ipData->message
            ];
    }

    private function mongoVisitorExists(Client $client, $collection)
    {
        $collection = $client->selectCollection(Config::get('database.mongodb.database'), $collection);
        return $collection->countDocuments(['ip' => $this->getIP(), 'current' => Config::currentUrl()]);
    }

}