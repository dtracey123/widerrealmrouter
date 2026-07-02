<?php
namespace Widerrealm\Router;

interface RequirementInterface {
	public function condition();
	public function fail($router);
}