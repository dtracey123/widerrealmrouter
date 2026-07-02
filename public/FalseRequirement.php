<?php
namespace Public;

use Widerrealm\Router\Requirement;

class FalseRequirement extends Requirement {
    public function condition() {
        return false;
    }

	public function fail($router) {
		return ['route' => $router->get_current_route()];
	}
}