<?php

namespace Nmr\Mobile\Controller;
use Nmr;

class CartController extends \Nmr\Application\Controller {

	/**
	 * Shows Cart Summary
	 */
	public function index()
	{
		$this->requireSession(function($session_id) {

			$result = $this->api->get('/shoppingcart/'.$session_id);

			if ($result['error'] == 0) {

				$result['data']['item_count'] = count($result['data']['items']);
				$cartSummaryTemplate = $this->getHandlebarsTemplate('cart/cart-summary.hbs');
				$cartSummaryHtml = $this->renderHandlebars($cartSummaryTemplate, ['cart' => $result['data']]);

				$this->render([
					'cart_summary_template' => $cartSummaryTemplate,
					'cart_summary_html' => $cartSummaryHtml,
					'cart' => $result['data'],
					'js_options' => [
						'cart' => $result['data'],
						'next_url' => '/addresses'
					]
				]);

			} else {
				$this->render([
					'cart' => [],
					'js_options' => [
						'cart' => [],
						'next_url' => '/addresses'
					]]);
			}
		});
	}

	/**
	 * Add Item to Cart
	 */
	public function add()
	{
		$this->requireSession(function($session_id){

			$post = $this->getPost();
			$post['session_id'] = $session_id;

			$result = $this->api->post('/shoppingcart', $post);

			if($result['error'] == 1) {

				$this->renderJson([
					'status' => 1 ,
					'error'  => $result['message'],
					'payload' => $result
				]);

			}else{

				$this->session->createCartSession($result['data']['id']);

				$this->renderJson([
					'status' => 0 ,
					'session_id' => $session_id,
					'shopping_cart_id' => $result['data']['id']
				]);
			}
		});
	}

	/**
	 * Remove Item from Cart
	 */
	public function remove()
	{
		$this->requireSession(function($session_id){

			$post = $this->getPost();
			$post['session_id'] = $session_id;

			$result = $this->api->delete('/shoppingcart', $post);

			if($result['error'] == 1) {

				$this->renderJson([
					'status' => 1 ,
					'error'  => $result['message'],
					'payload' => $result
				]);

			}else{

				$this->renderJson([
					'status' => 0,
					'cart_summary' => $result['data']['cart_summary']
				]);
			}
		});
	}

	/**
	 * Update Cart Item
	 */
	public function update()
	{
		$this->requireSession(function($session_id){

			$post = $this->getPost();
			$post['session_id'] = $session_id;

			$result = $this->api->put('/shoppingcart', $post);

			if($result['error'] == 1) {

				$this->renderJson([
					'status' => 1 ,
					'error'  => $result['message'],
					'payload' => $result
				]);

			}else{

				$this->renderJson([
					'status' => 0 ,
					'updated_item' => $result['data']['updated_item'],
					'cart_summary' => $result['data']['cart_summary']
				]);
			}
		});
	}
}