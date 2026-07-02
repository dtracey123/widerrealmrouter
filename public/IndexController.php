<?php 
namespace Public;

class IndexController {
	public function web() {
		return 'web';
	}

	public function data($data) {
		return $data;
	}
}