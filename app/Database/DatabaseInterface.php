<?php

namespace App\Database;

interface DatabaseInterface
{
    public function insert($data, $sql);

    public function get($sql, $options, $filters);

    public function update($sql);

    public function delete($sql, $filter, $options);

    public function table($table);
}