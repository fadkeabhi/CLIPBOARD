<?php

$configFile = __DIR__ . '/config.php';

if (!file_exists($configFile)) {
    die('config.php does not exist');
}

/**
 * @var $servername string
 * @var $username string
 * @var $password string
 * @var $dbname string
 */
require_once $configFile;

// Create connection
$conn = new mysqli("localhost", "root", "sneha", "clipboard");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: $conn->connect_error");
}
