<?php
namespace Nmr;

class Deals {

	public function fetch($offset=0, $limit=4, $category=false)
	{
		$deals = $this->all();

		if ($category) {
			$items = [];
			foreach($deals as $deal) {
				if (strtolower($deal['category']) == strtolower($category)) {
					$items[] = $deal;
				}
			}

			$deals = $items;
		}
		return array_splice($deals, $offset, $limit);
	}

	public function all()
	{
		$deals = require 'data/deals.php';

		$fake_attributes = [
			'Size' => ['Small','Medium','Large', 'X-Large'],
			'Color' => ['Red','Green','Purple', 'Brown'],
			'Pattern' => ['Striped','Solid', 'Checkered']
		];

		foreach($deals as $id => $deal) {
			$deals[$id]['deal_id'] = $id;
			$deals[$id]['product_id'] = preg_replace("/[^0-9]/","", strrchr($deal['image'], '/'));
			$deals[$id]['attributes'] = $fake_attributes;
			$deals[$id]['url'] = "/deals/" . $id . "/" . $this->sanitizeTitle($deal['title']);
			$deals[$id]['image'] = 'http://static5.nmr.allcdn.net/images/products/'. $deals[$id]['product_id'] . '-dd.jpg';
			$deals[$id]['image_count'] = 5;
		}

		return $deals;
	}

	public function find($deal_id)
	{
		$deals = $this->all();
		return $deals[$deal_id];
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