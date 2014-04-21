<?php

class ApplicationTest extends PHPUnit_Framework_TestCase {

	public function testIndex()
	{
		//makes sure that framework calls index/index if no path is given
		//e.g. if they type m.nmr.com (w/ no uri), it should map to index/index

		$app = new \Nmr\Application();
		$app->getController('/');

		$this->assertEquals('index', $app->getRoute());
		$this->assertEquals('index', $app->getAction());

	}

	public function testPaths()
	{
		//makes sure that framework calls route/index if no action is given
		$app = new \Nmr\Application();
		$app->getController('/checkout/');

		$this->assertEquals('checkout', $app->getRoute());
		$this->assertEquals('index', $app->getAction());

	}

	public function testIdPath()
	{
		//makes sure that framework calls route/index if id is given
		//direcly after path

		$app = new \Nmr\Application();
		$app->getController('/products/12142');

		$this->assertEquals('products', $app->getRoute());
		$this->assertEquals('index', $app->getAction());

	}

	public function testControllerActionPath()
	{
		//makes sure that framework calls route/index if id is given
		//direcly after path

		$app = new \Nmr\Application();
		$app->getController('/products/view');

		$this->assertEquals('products', $app->getRoute());
		$this->assertEquals('view', $app->getAction());

	}

	public function testControllerActionWithIdPath()
	{
		//makes sure that framework calls route/index if id is given
		//direcly after path and that trailing paths do not affect route/action

		$app = new \Nmr\Application();
		$app->getController('/products/view/121412');

		$this->assertEquals('products', $app->getRoute());
		$this->assertEquals('view', $app->getAction());

	}
}