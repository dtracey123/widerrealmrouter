<?php
require_once(__DIR__ . './../vendor/autoload.php');

include('Routes.php');
include('./TrueRequirement.php');
include('./FalseRequirement.php');
include('./IndexController.php');

$routes = new Routes();

$routes->register();
// execute route if it's found.
$routes->execute();
// 404 if no routes are found
$routes->error_handler()->route_not_found();