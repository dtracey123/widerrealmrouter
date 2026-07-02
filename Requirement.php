<?php
namespace Widerrealm\Router;

class Requirement implements RequirementInterface {
    public function condition() {
        return false;
    }

    public function fail($router) {
        $router->error_handler()->route_not_authorised();
    }

    public function success($router) {
        
    }
}