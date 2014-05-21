<?php

return [

	#deals aka Home page
	['get'  => ['/', 'Deals', 'index']],

	['get'  => ['/deals/category/:category', 'Deals', 'category']],
	['get'  => ['/deals/:id/:seo_title', 'Deals', 'view']],
	['get'  => ['/deals/fetch', 'Deals', 'fetch']],

	#register
	['get' =>  ['/register', 'Auth', 'registerView']],
	['post' => ['/register', 'Auth', 'registerSubmit']],

	#login
	['get'  => ['/login', 'Auth', 'loginView']],
	['post' => ['/login', 'Auth', 'loginSubmit']],
	['post' => ['/login/fbconnect', 'Auth', 'fbConnect']],
	['get' =>  ['/logout', 'Auth', 'logout']],

];