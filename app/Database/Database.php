<?php

namespace App\Database;


use App\Database\Crud\CrudMongoDB;
use MongoDB\Client;
use MongoDB\Driver\Exception\AuthenticationException;
use MongoDB\Driver\Exception\ConnectionException;
use App\Config\Config;

class Database extends DatabaseAbstract implements DatabaseInterface
{
    protected $driver;
    protected $config;
    protected $db;
    protected $table;

    public function __construct($driver = 'mongodb', array $config = [])
    {
        $this->driver = $driver;
        $this->config = $config;
    }

    public function connect()
    {
        if ($this->driver === 'mongodb') {
            if (!Config::get('database.mongodb.ext-mongodb'))
                throw new \Exception('Mongodb extension is not loaded');
            try {
                $this->db = new Client(Config::get('database.mongodb.uri'));
                return $this->db;
            } catch (AuthenticationException $e) {
                return $e->getMessage();
            } catch (ConnectionException $e) {
                return $e->getMessage();
            }
        }
        return false;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function insert($data, $sql = '')
    {
        if ($this->driver === 'mongodb')
            return CrudMongoDB::insert($this->db, $this->table, $data);
    }

    public function get($sql = '', $filters = [], $options = [])
    {
        if ($this->driver === 'mongodb')
            return CrudMongoDB::get($this->db, $this->table, $filters, $options);
    }

    public function update($data, $sql = '', $filter = [], $options = [])
    {
        if ($this->driver === 'mongodb')
            return CrudMongoDB::update($this->db, $this->table, $data, $filter, $options);
    }

    public function delete($sql = '', $filter = [], $options = [])
    {
        if ($this->driver === 'mongodb')
            return CrudMongoDB::delete($this->db, $this->table, $filter, $options);
    }
}