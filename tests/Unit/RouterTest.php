<?php
use Widerrealm\Router\Router;

class RouterTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private $router;
    
    protected function _before()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test';

        $this->router = new Router();
        $this->router->get('/test', fn() => 'test', name: 'test.test');
        $this->router->get('/person/{string:name}/{int:age}', fn() => 'test', name: 'name.param');

		$this->router->get('/char/{char:a}/char/{char:b}/{char:c}/char', fn($a, $b, $c) => "$a-$b-$c", name: 'char.complex');

		$this->router->get('/int/{int:a}/int/{int:b}/{int:c}/int', fn($a, $b, $c) => "$a-$b-$c", name: 'int.complex');

		$this->router->get('/string/{a}/string/{b}/{c}/string', fn($a, $b, $c) => "$a-$b-$c", name: 'string.complex');

		$this->router->get('/float/{float:a}/float/{float:b}/{float:c}/float', fn($a, $b, $c) => "$a-$b-$c", name: 'float.complex');
    }

    protected function _after()
    {
    }

    public function test_get_current_route() {
        $this->assertEquals(Router::get_current_route(), '/test');
    }

    public function test_router_find() {
        $this->assertEquals(Router::find('test.test'), '/test');
    }

    public function test_router_exists() {
        $this->assertEquals(Router::exists('test.test'), true);

        $this->assertEquals(Router::exists('text.text'), false);
    }

    public function test_router_find_params() {
        $this->assertEquals(Router::find('name.param', ['name' => 'john', 'age' => 20]), '/person/john/20');
    }

    public function test_router_find_params_string() {
        $this->assertEquals(Router::find('string.complex', ['a' => 'john', 'b' => 'doe', 'c' => 'jane']), '/string/john/string/doe/jane/string');
    }

    public function test_router_find_params_char() {
        $this->assertEquals(Router::find('char.complex', ['a' => 'a', 'b' => 'b', 'c' => 'c']), '/char/a/char/b/c/char');
    }

    public function test_router_find_params_int() {
        $this->assertEquals(Router::find('int.complex', ['a' => 1, 'b' => 2, 'c' => 3]), '/int/1/int/2/3/int');
    }

    public function test_router_find_params_float() {
        $this->assertEquals(Router::find('float.complex', ['a' => 1.1, 'b' => 2.22, 'c' => 3.333]), '/float/1.1/float/2.22/3.333/float');
    }

    public function test_routes_is_array() {
        $this->assertIsArray(Router::get_routes());
    }
}