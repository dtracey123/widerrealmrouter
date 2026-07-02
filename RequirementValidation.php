<?php 
namespace Widerrealm\Router;

class RequirementValidation {
    public function __construct(private $requirement, private $router) { }

    public function validated() {
        if($this->requirement == null) {
            return true;
        } else {
            return $this->validate_requirement();
        }
    }

    private function validate_requirement() {
        $requirement = new $this->requirement;

        if($requirement->condition() === true) {
            $requirement->success($this->router);

            return true;
        }

        if(is_null($requirement->fail($this->router))) {
            $requirement->fail($this->router);
            die();
        }

        if(is_string($requirement->fail($this->router))) {
            http_response_code(401);
            echo $requirement->fail($this->router);
            die();
        }

        if(is_array($requirement->fail($this->router))) {
            Router::json_response($requirement->fail($this->router), 401);
            die();
        }
    }
}