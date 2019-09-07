<?php


namespace App\Visitors;


interface RixVisitorsInterface
{
    public function getInfo($api);

    public function getIP();

    public function getBrowser();

    public function getReferrer();

    public function getGeolocationInfo();

    public function getIpDataInfo($key);

    public function fetch($table, $driver);
}