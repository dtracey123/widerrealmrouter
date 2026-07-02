<?php

namespace Widerrealm\Router;

interface RouterInterface {
	public function group($register_routes, $requirement = null, $prefix = null);

    public function redirect($routes, $url, $callback = null);

    public function permanent_redirect($routes, $url);

    public function any($route, $callback_function, $requirement = null, $name = null);

    public function get($route, $callback_function, $requirement = null, $name = null);

    public function post($route, $callback_function, $requirement = null, $name = null);

    public function put($route, $callback_function, $requirement = null, $name = null);

    public function patch($route, $callback_function, $requirement = null, $name = null);

    public function delete($route, $callback_function, $requirement = null, $name = null);
    
    public function execute();

    public function error_handler();

    public function set_error_handler($class);
}