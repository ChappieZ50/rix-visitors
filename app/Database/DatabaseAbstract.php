<?php

namespace App\Database;

abstract class DatabaseAbstract
{
    protected $driver;
    protected $config;
    protected $db;
    protected $table;
    abstract function __construct($driver, array $config);
    abstract function connect();
}