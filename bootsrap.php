<?php
require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//model path
$paths = array(__DIR__."/src");
$isDevMode = false;

// doctrine ORM the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'dbname'   => $_ENV['DB_NAME'],
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

// PDO connection for direct query
try {
    $host = $_ENV['DB_HOST'];
    $dbName = $_ENV['DB_NAME'];
    $conn = new PDO("mysql:host=$host;dbname=$dbName", $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    
  }