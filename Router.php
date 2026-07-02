<?php
namespace Widerrealm\Router;

class Router implements RouterInterface {
    /*
    * Static Vars
    */
    private static Array $param_delimiters = ['{', '}'];
    private static String $param_data_type_delimiter = ':';

    private static String $param_regex_all = '[^\/]*';

    private static Array $routes = [];

    private $error_handler = RouterErrorHandler::class;

    /*
    * Non Static Vars
    */
    private $found_route = null;

    private $group_requirement = null;
    private $group_prefix = null;

    private Array $param_data_types = [
        'int' => '(?<![\d.])[0-9]+(?![\d.])',
        'decimal' => '\d+\.\d*',
        'string' => '[^0-9]*',
        'char' => '.'
    ];

    public function __construct() {
        $this->error_handler = new $this->error_handler;
    }
    
    /*
    * Static Functions
    */

    /*
    * Public
    */

    public static function get_routes() : Array {
        return self::$routes;
    }

    public static function exists($route_name, $return_index = false) {
        $index = array_search($route_name, self::get_route_names(), true);

        if($index === false) {
            return false;
        }

        return $return_index ? $index : true;
    }
    
    public static function json_response($data, $error_code = 200) {
        http_response_code(intval($error_code));

        self::jd($data);
    }

    public static function find($route_name, $params = null) {
        if(Router::exists($route_name)) {
            $index = Router::exists($route_name, true);
            $found_route_uri = self::$routes[$index]['route'];

            $return = $found_route_uri;

            if(is_array($found_route_uri)) {
                $return = $found_route_uri[0];
            }

            if(is_array($params)) {
                $exploded_array = explode('/', $return);
                
                $index = 0;
                
                foreach($exploded_array as $section) {
                    if(str_contains($section, self::$param_delimiters[0]) && str_contains($section, self::$param_delimiters[1])) {
                        foreach($params as $key=>$value) {
                            if(str_contains($section, self::$param_data_type_delimiter)) {
                                $exploded_section = explode(self::$param_data_type_delimiter, $section);
                                if(str_contains($exploded_section[1], $key)) {
                                    $exploded_array[$index] = $value;
                                }
                                
                            } else {
                                if(str_contains($section, $key)) {
                                    $exploded_array[$index] = $value;
                                }
                            }
                        }
                    }
                    $index++;
                }
                
                $return = implode('/', $exploded_array);
            }

            return $return;
        }

        return null;
    }

    public function set_param_data_types($data_types_array) {
        if(!empty($data_types_array)) {
            $this->param_data_types = $data_types_array;
        }
    }


    public static function get_http_request_method() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public static function get_current_route() : String {
        $route = $_SERVER['REQUEST_URI'];
        $route_array = parse_url($route);
        if(isset($route_array['path'])) {
            $route = $route_array['path'];
            return $route;
        } 
        return false;
    }

    /*
    * Private
    */

    private static function get_route_names() {
        return array_map(function($route) {
            return $route['name'];
        }, self::$routes);
    }

    private static function explode_route($route) {
        $exploded_route = explode('/', $route);
        array_shift($exploded_route);
        return $exploded_route;
    }
    
    /*
    * Non Static Functions
    */

    /*
    * Public
    */

    public function group($register_routes, $requirement = null, $prefix = null) {
        $this->group_requirement = $requirement;
        $this->group_prefix = $prefix;
        
        $register_routes();

        $this->group_requirement = null;
        $this->group_prefix = null;
    }
    
    public function prefix($prefix, $routes) {
        $this->group_prefix = $prefix;
        
        $routes();

        $this->group_prefix = null;
    }

    public function redirect($routes, $url, $callback = null) {
        $this->redirect_routes($routes, $url, 302, $callback);
    }

    public function permanent_redirect($routes, $url) {
        $this->redirect_routes($routes, $url, 301);
    }

    public function any($route, $callback_function, $requirement = null, $name = null) {
        $this->serve_request(null, $route, $callback_function, $requirement, $name);
    }

    public function get($route, $callback_function, $requirement = null, $name = null) {
        $this->serve_request('GET', $route, $callback_function, $requirement, $name);
    }

    public function post($route, $callback_function, $requirement = null, $name = null) {
        $this->serve_request('POST', $route, $callback_function, $requirement, $name);
    }

    public function put($route, $callback_function, $requirement = null, $name = null) {
        $this->serve_request('PUT', $route, $callback_function, $requirement, $name);
    }

    public function patch($route, $callback_function, $requirement = null, $name = null) {
        $this->serve_request('PATCH', $route, $callback_function, $requirement, $name);
    }

    public function delete($route, $callback_function, $requirement = null, $name = null) {
        $this->serve_request('DELETE', $route, $callback_function, $requirement, $name);
    }

    public function execute() {
        if($this->found_route !== null) {
            if(is_object($this->found_route)) {
                $this->execute_callback($this->found_route, []);
            }

            if(isset($this->found_route['action']) && isset($this->found_route['data'])) {
                $this->execute_function_or_controller($this->found_route['action'], $this->found_route['data']);
            } else {
                $this->execute_function_or_controller($this->found_route);
            }
            
            die();
        }
    }

    public function error_handler() {
        return $this->error_handler;
    }

    public function set_error_handler($class) {
        $this->error_handler = new $class;
    }
    
    /*
    * Private
    */

    private function redirect_routes($routes, $url, $status_code, $callback = null) {
        if($this->group_prefix && !is_array($routes)) {
            $routes = $this->group_prefix . $routes;
        }

        if(is_array($routes) && $this->group_prefix) {
            $routes = array_map(function($route) {
                return $this->group_prefix . $route;
            }, (array)$routes);
        }

        if($this->validate_route($routes) && !is_array($routes)) {
            $this->redirect_user($callback, $url, $status_code);
        }
        
        if(is_array($routes)) {
            foreach($routes as $route) {
                if($this->validate_route($route)) {
                    $this->redirect_user($callback, $url, $status_code);
                }
            }
        }
    }

    private function redirect_user($callback, $url, $status_code) {
        if($callback != null) {
            $callback();
        }
        header("Location: {$url}", true, $status_code);
        die();
    }

    private static function validate_requirement($requirement, $router) {
        $requirement_obj = new RequirementValidation($requirement, $router);

        if($requirement_obj->validated()) {
            return true;
        }
    }

    private function validate_route($route, $requirement = null) {
        if(self::get_current_route() == $route) {
            if(self::validate_requirement($requirement, $this)) {
                return true;
            }
        }
        return false;
    }

    private static function jd($param) {
        header('Content-type: application/json');
        die(json_encode($param));
    }

    public static function return_response($data, $error_code) {
        if(is_string($data)) {
            echo $data;
            http_response_code($error_code);
        } else {
            self::json_response($data, $error_code);
        }

        die();
    }

    private function execute_function_or_controller($action, $data_array = []) {
        // If the action is a callback we just execute the function and pass in the data.
        if(is_object($action)) {
            $this->execute_callback($action, $data_array);
        }

        // If it's a controller we validate the controller, and if it's valid, we load the controler with the data.
        if(is_array($action)) {
            if($this->action_is_function($action)){
                $this->execute_function($action, $data_array);
            } 
        }
        die();
    }

    private function execute_callback($action, $data_array) {
        // Start the output buffering
        ob_start();

        $response = call_user_func_array($action, $data_array);

        // Get the contents of the output bugger
        $block = ob_get_contents();

        // Stop the output buffer but keep the contents
        ob_end_flush();

        // If there is content in the output buffer we know the application has rendered content for us so we don't need to do anything.
        if($block === '') {
            if(!is_string($response)) {
                self::json_response($response);
            }

            echo $response;
        }
        die();
    }

    private function action_is_function($action) {
        if(sizeof($action) == 2) {
            return true;
        } 

        return false;
    }

    private function execute_function($action, $data_array) {
        // Start the output buffering
        ob_start();

        $controller = $action[0];

        $function = $action[1];
        
        $obj = new $controller;

        $output = $obj->$function(...$data_array);
        
        // Get the contents of the output bugger
        $block = ob_get_contents();

        // Stop the output buffer but keep the contents
        ob_end_flush();

        // If there is content in the output buffer we know the application has rendered content for us so we don't need to do anything.
        if($block === '') {
            if(gettype($output) == 'string') {
                echo($output);
            } else {
                self::json_response($output);
            }
        }
        
        die();
    }

    private function serve_request($request_method, $routes, $action, $requirement, $name) {
        if($this->group_requirement !== null) {
            $requirement = $this->group_requirement;
        }

        if($this->group_prefix !== null) {
            if(is_array($routes)) {
                $routes = array_map(function($route) {
                    return $this->group_prefix . $route;
                }, $routes);
            } else {
                $routes = $this->group_prefix . $routes;
            }
        }

        array_push(self::$routes, [
           'http_verb' => $request_method,
           'route' => $routes,
           'requirement' => $requirement,
           'name' => $name,
           'controller' => $action,
        ]);
        
        if($this->found_route === null) {
            if(is_array($routes)) {
                foreach($routes as $route) {
                    $this->verify_route($request_method, $route, $requirement, $action);
                }
            } else {
                $this->verify_route($request_method, $routes, $requirement, $action);
            }
        }
        
    }

    private static function verify_http_request_method($http_method) : bool {
        if($http_method == null) {
            return true;
        }
     
        if(self::get_http_request_method() === $http_method) {
            return true;
        }

        return false;
    }

    private function verify_route($request_method, $route, $requirement, $action) {
        if(self::verify_http_request_method($request_method)) {
            if($this->validate_route($route, $requirement)) {
                $this->found_route = $action;
            }

            $this->validate_route_with_param($route, $action, $requirement);
        }
    }

    private function validate_route_with_param($route, $action, $requirement) {
        if(strpos($route, self::$param_delimiters[0]) && strpos($route, self::$param_delimiters[1])) {
            $exploded_route = self::explode_route($route);
            $exploded_current_route = self::explode_route(self::get_current_route());

            $data_array = [];
            $regex_string = '';

            $left_param_delimiter = self::$param_delimiters[0];

            foreach($exploded_route as $index=>$section) {
                if(strpos($section, $left_param_delimiter) !== false) {
                    $param_regex = self::$param_regex_all;

                    if(strpos($section, self::$param_data_type_delimiter)) {
                        $param_regex = $this->validate_param_data_type($section);
                    }

                    array_push($data_array, [
                        'key' => $this->remove_braces($section),
                        'value' => $index
                    ]);

                    $section = $param_regex;
                }

                $regex_string .= '\/' . $section;
            }
            
            $this->verify_route_with_query($regex_string, $action, $data_array, $exploded_current_route, $requirement);
        }
    }

    private function remove_braces($key) {
        $size_of_key = strlen($key);

        $key = substr($key, 1, $size_of_key - 2);

        if(str_contains($key, self::$param_data_type_delimiter)) {
            $key = explode(self::$param_data_type_delimiter, $key)[1];
        }

        return $key;
    }

    private function validate_param_data_type($section) {
        $pos_data_type_delim = strpos($section, self::$param_data_type_delimiter);
        $length_of_data_type = $pos_data_type_delim - 1;
        
        $param_data_type = substr($section, 1, $length_of_data_type);

        foreach($this->param_data_types as $key => $regex) {
            if($key == $param_data_type) {
                return $regex;
            }
        }

        trigger_error('syntax error "'. htmlspecialchars($param_data_type) . '" is an invalid data type.');
        return self::$param_regex_all;
    }

    private function verify_route_with_query($regex_string, $action, $data_array, $exploded_current_route, $requirement) {
        $regex_string = '/^' . $regex_string . '$/';

        $lower_case_regex_string = strtolower($regex_string);
        $lower_case_current_route = strtolower(self::get_current_route());

        if(preg_match($lower_case_regex_string, $lower_case_current_route)) {
            $final_data_array = [];

            foreach($data_array as $index) {
                $value = $exploded_current_route[intval($index['value'])];
                
                if(is_string($value)) {
                    $value = htmlspecialchars($value);
                }

                array_push($final_data_array, $value);
            }

            if(self::validate_requirement($requirement, $this)) {
                $this->found_route = [
                    'action' => $action,
                    'data' => $final_data_array,
                ];
            } 
        }
    }
}