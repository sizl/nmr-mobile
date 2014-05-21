<?php

class DealsTest extends PHPUnit_Framework_TestCase {

	protected $dealsModel;

	public function setUp()
	{
		$config = require APP_ROOT . '/config/config.php';
		$apiClient = new Nmr\ApiClient($config['api']);

		$this->dealsModel = new Nmr\Deals($apiClient);
	}

	public function testFetch()
	{
		$result = $this->dealsModel->fetch(0, 10);

		$this->assertEquals(10, count($result));
		$firstElement = current($result);

		$this->assertArrayHasKey('category', $firstElement);
		$this->assertArrayHasKey('name', $firstElement);
		$this->assertArrayHasKey('price', $firstElement);
		$this->assertArrayHasKey('retail', $firstElement);
		$this->assertArrayHasKey('savings', $firstElement);
		$this->assertArrayHasKey('url', $firstElement);
		$this->assertArrayHasKey('image', $firstElement);
	}

	public function testFetchByCategory()
	{
		$result = $this->dealsModel->fetch(0, 5, 'Women');

		$this->assertEquals(5, count($result));
		$firstElement = current($result);

		$this->assertEquals('Women', $firstElement['category']);

		$this->assertArrayHasKey('category', $firstElement);
		$this->assertArrayHasKey('name', $firstElement);
		$this->assertArrayHasKey('price', $firstElement);
		$this->assertArrayHasKey('retail', $firstElement);
		$this->assertArrayHasKey('savings', $firstElement);
		$this->assertArrayHasKey('url', $firstElement);
		$this->assertArrayHasKey('image', $firstElement);
	}

	public function testFind()
	{
		$result = $this->dealsModel->fetch(0, 3);
		$deal_ids = array_keys($result);

		//now test to make sure find() works
		$deal = $this->dealsModel->find($deal_ids[0]);

		$this->assertNotEmpty($deal);
		$this->assertArrayHasKey('deal_id', $deal);
		$this->assertArrayHasKey('product_id', $deal);
		$this->assertArrayHasKey('category', $deal);
		$this->assertArrayHasKey('name', $deal);
		$this->assertArrayHasKey('description', $deal);
		$this->assertArrayHasKey('price', $deal);
		$this->assertArrayHasKey('retail', $deal);
		$this->assertArrayHasKey('savings', $deal);
	    $this->assertArrayHasKey('shipping', $deal);
	    $this->assertArrayHasKey('attributes', $deal);
	    $this->assertArrayHasKey('url', $deal);
	    $this->assertArrayHasKey('image', $deal);
	    $this->assertArrayHasKey('image_count', $deal);

	}

}
