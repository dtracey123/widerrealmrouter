<?php
namespace Widerrealm\Router;

class RouterErrorHandler implements RouterErrorHandlerInterface {
	public function route_not_found() {
		self::display_status(404, [
			'message' => 'Route not found'
		]);
	}

	public function route_not_authorised() {
		self::display_status(401, [
			'message' => 'Route not authorised'
		]);
	}

	public function method_not_allowed() {
		self::display_status(405, [
			'message' => 'Method Not Allowed'
		]);
	}

	private function display_status($error_code, $controller) {    
        $route = Router::get_current_route();
        $escaped_route = [htmlspecialchars($route)];

        if($controller) {
            Router::return_response($controller, $error_code, $escaped_route);
        } 

        http_response_code($error_code);
        die('Cannot ' . Router::get_http_request_method() . ' ' . htmlspecialchars($route));    
    }
}