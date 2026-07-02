<?php
use Public\TrueRequirement;
use Public\FalseRequirement;
use Public\IndexController;

use Widerrealm\Router\Router;

class Routes extends Router {
	public function register() {
		$this->any('/any', fn() => 'any');
		$this->get('/get', fn() => 'get');
		$this->post('/post', fn() => 'post');
		$this->put('/put', fn() => 'put');
		$this->patch('/patch', fn() => 'patch');
		$this->delete('/delete', fn() => 'delete');
		
		$this->get('/int/{int:value}', fn($value) => $value);
		$this->get('/decimal/{decimal:value}', fn($value) => $value);
		$this->get('/string/{value}', fn($value) => $value, name: 'route.string');
		$this->get('/char/{char:value}', fn($value) => $value);
		$this->get('/any/{value}', fn($value) => $value);

		$this->get('/json', fn() => ['data' => ['nested' => ['array' => ['of' => ['data' => 'here']]]]]);
		$this->get('/json/{string}/{int:int}/{decimal:decimal}/{char:char}', fn($string, $int, $decimal, $char) => ['string' => $string, 'int' => $int, 'decimal' => $decimal, 'char' => $char]);
		
		$this->get(['/there', '/are', '/many'], fn() => 'many');
		$this->get(['/many/int/{int:value}', '/many/{int:value}', '/many/{int:value}/complex/url'], fn($value) => $value);
		
		$this->get('/true', fn() => 'yes', TrueRequirement::class);
		$this->get('/false', fn() => 'no', FalseRequirement::class);
		
		$this->get('/all', fn() => Router::get_routes());
		
		$this->get('/name', fn() => Router::find('route.name'), name: "route.name");
		
		$this->get('/controller', [IndexController::class, 'web']);
		$this->get('/controller/{data}', [IndexController::class, 'data']);		

		$this->get('/test/{int:number}/complex/{decimal:decimal}/{char:char}/test', fn($number, $decimal, $char) => "$number-$decimal-$char", name: 'route.complex');

		$this->redirect('/find-complex', Router::find('route.complex', ['number' => '101', 'decimal' => '1.1', 'char' => 'x']));
		$this->redirect('/find-simple', Router::find('route.string', ['value' => 'abc']));

		$this->redirect('/redirect', Router::find('route.name'));
		$this->permanent_redirect('/permanent-redirect', Router::find('route.name'));

		$this->get('/char/{char:a}/char/{char:b}/{char:c}/char', fn($a, $b, $c) => "$a-$b-$c", name: 'char.complex');
		$this->redirect('/char/complex', Router::find('char.complex', ['a' => 'a', 'b' => 'b', 'c' => '1']));

		$this->get('/int/{int:a}/int/{int:b}/{int:c}/int', fn($a, $b, $c) => "$a-$b-$c", name: 'int.complex');
		$this->redirect('/int/complex', Router::find('int.complex', ['a' => '1', 'b' => '22', 'c' => '333']));

		$this->get('/string/{a}/string/{b}/{c}/string', fn($a, $b, $c) => "$a-$b-$c", name: 'string.complex');
		$this->redirect('/string/complex', Router::find('string.complex', ['a' => 'a', 'b' => 'b', 'c' => 'c']));

		$this->get('/decimal/{decimal:a}/decimal/{decimal:b}/{decimal:c}/decimal', fn($a, $b, $c) => "$a-$b-$c", name: 'decimal.complex');
		$this->redirect('/decimal/complex', Router::find('decimal.complex', ['a' => '1.1', 'b' => '2.22', 'c' => '3.333']));

		$this->prefix('/prefix', function() {
			$this->any('/any', fn() => 'any');
			$this->any('/true', fn() => 'true', TrueRequirement::class);
			$this->any('/false', fn() => 'false', FalseRequirement::class);
		});

		$this->group(function() {
			$this->any('/any', fn() => 'any');
			$this->get('/get', fn() => 'get');
			$this->post('/post', fn() => 'post');
			$this->put('/put', fn() => 'put');
			$this->patch('/patch', fn() => 'patch');
			$this->delete('/delete', fn() => 'delete');

			$this->get('/int/{int:value}', fn($value) => $value);
			$this->get('/decimal/{decimal:value}', fn($value) => $value);
			$this->get('/string/{value}', fn($value) => $value);
			$this->get('/char/{char:value}', fn($value) => $value);
			$this->get('/any/{value}', fn($value) => $value);

			$this->get(['/there', '/are', '/many'], fn() => 'many');

			$this->redirect('/redirect', Router::find('route.name'));
			$this->permanent_redirect('/permanent-redirect', Router::find('route.name'));
		}, TrueRequirement::class, '/true');

		// a group of all the route options attatched to a requirement that returns false.
		$this->group(function() {
			$this->any('/any', fn() => 'any');
			$this->get('/get', fn() => 'get');
			$this->post('/post', fn() => 'post');
			$this->put('/put', fn() => 'put');
			$this->patch('/patch', fn() => 'patch');
			$this->delete('/delete', fn() => 'delete');

			$this->get('/int/{int:value}', fn($value) => $value);
			$this->get('/decimal/{decimal:value}', fn($value) => $value);
			$this->get('/string/{value}', fn($value) => $value);
			$this->get('/char/{char:value}', fn($value) => $value);
			$this->get('/any/{value}', fn($value) => $value);

			$this->get(['/there', '/are', '/many'], fn() => 'many');

			$this->redirect('/redirect', Router::find('route.name'));
			$this->permanent_redirect('/permanent-redirect', Router::find('route.name'));
		}, FalseRequirement::class, '/false');
	}
}