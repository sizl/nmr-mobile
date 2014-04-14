<?php
namespace Nmr;

class Deals {

	public function fetch()
	{
		$deals = require 'data/deals.php';

		$fake_attributes = [
			'Size' => ['Small','Medium','Large', 'X-Large'],
			'Color' => ['Red','Green','Purple', 'Brown'],
			'Pattern' => ['Striped','Solid', 'Checkered']
		];

		foreach($deals as $id => $deal) {
			$deals[$id]['product_id'] = preg_replace("/[^0-9]/","", strrchr($deal['image'], '/'));
			$deals[$id]['attributes'] = $fake_attributes;
			$deals[$id]['seo_friendly_title'] = $this->sanitizeTitle($deal['title']);
			$deals[$id]['image_count'] = 5;
		}

		return $deals;
	}

	function sanitizeTitle($string, $force_lowercase = true, $anal = false) {
		$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
			"}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
			"â€”", "â€“", ",", "<", ".", ">", "/", "?");
		$clean = trim(str_replace($strip, "", strip_tags($string)));
		$clean = preg_replace('/\s+/', "-", $clean);
		$clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;

		return ($force_lowercase) ?
			(function_exists('mb_strtolower')) ?
				mb_strtolower($clean, 'UTF-8') :
				strtolower($clean) :
			$clean;
	}
}