<?php
require_once __DIR__ . "/vendor/autoload.php";

$visitors = new \App\Visitors\RixVisitors();
$visitors->fetch('users');
