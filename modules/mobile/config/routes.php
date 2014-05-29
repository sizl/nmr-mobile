<?php

return [

	#deals aka Home page
	['get'  => ['/', 'Deals', 'index']],

	['get'  => ['/deals/category/:category', 'Deals', 'category']],
	['get'  => ['/deals/:id/:seo_title', 'Deals', 'view']],
	['get'  => ['/deals/fetch', 'Deals', 'fetch']],
	['get'  => ['/deals/search', 'Deals', 'search']],

	#events
	['get'  => ['/events', 'Events', 'index']],
	['get'  => ['/events/fetch', 'Events', 'fetch']],
	['get'  => ['/events/:id/deals', 'Events', 'deals']],
	['get'  => ['/events/:id/:seo_title', 'Events', 'view']],

	#register
	['get' =>  ['/register', 'Auth', 'registerView']],
	['post' => ['/register', 'Auth', 'registerSubmit']],

	#login
	['get'  => ['/login', 'Auth', 'loginView']],
	['post' => ['/login', 'Auth', 'loginSubmit']],
	['post' => ['/login/fbconnect', 'Auth', 'fbConnect']],
	['get' =>  ['/logout', 'Auth', 'logout']],

	#cart
	['get'  => ['/cart', 'Cart', 'index']],
	['post' => ['/cart/add', 'Cart', 'add']],
	['post' => ['/cart/remove', 'Cart', 'remove']],
	['post' => ['/cart/update', 'Cart', 'update']],

	#recent
	['get'  => ['/recent', 'Recent', 'index']],
	['post' => ['/recent/add', 'Recent', 'add']]

];