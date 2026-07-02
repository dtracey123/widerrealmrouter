<?php
namespace Widerrealm\Router;
Interface RouterErrorHandlerInterface {
	public function route_not_found();
	public function route_not_authorised();
	public function method_not_allowed();
}