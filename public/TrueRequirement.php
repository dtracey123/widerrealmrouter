<?php
namespace Public;

use Widerrealm\Router\Requirement;

class TrueRequirement extends Requirement {
    public function condition() {
        return true;
    }

    public function fail($router) {
		return ['route' => $router->get_current_route()];
	}
}