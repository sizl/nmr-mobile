<?php

namespace Nmr;

class Events {

	public function fetch($offset=0, $limit = 3)
	{
		$events = $this->all();
		return array_splice($events, $offset, $limit);
	}

	public function all()
	{
		$events = require 'data/events.php';

		foreach($events as $id => $event) {
			$events[$id]['url'] = "/events/" . $id . "/" . $this->sanitizeTitle($event['title']);
		}

		return $events;
	}

	public function find($deal_id)
	{
		$deals = $this->all();
		return $deals[$deal_id];
	}

	//TODO: Make this a helper
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