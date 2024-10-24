<?php
include_once("configuration/Configuration.php");
$configuration = new Configuration();
$router = $configuration->getRouter();
session_start();

$router->route($_GET['page'], $_GET['action'] ?? '');