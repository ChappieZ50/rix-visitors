<?php

namespace App\Database\Crud;

use App\Config\Config;

class CrudMongoDB
{
    static protected $data;

    public static function insert($db, $collection, $data)
    {
        $collection = $db->selectCollection(Config::get('database.mongodb.database'), $collection);
        return $collection->insertOne($data);
    }

    public static function get($db, $collection, $filter, $options)
    {
        $collection = $db->selectCollection(Config::get('database.mongodb.database'), $collection);
        return $collection->find($filter, $options)->toArray();
    }

    public static function update($db, $collection, $data, $filter, $options)
    {
        $collection = $db->selectCollection(Config::get('database.mongodb.database'), $collection);
        return $collection->updateOne($filter, $data, $options);
    }

    public static function delete($db, $collection, $filter, $options)
    {
        $collection = $db->selectCollection(Config::get('database.mongodb.database'), $collection);
        return $collection->deleteOne($filter, $options);
    }

}