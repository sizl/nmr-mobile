<?php


namespace Nmr\Mobile\Controller;
use Nmr\Events;

class EventsController extends \Nmr\Application\Controller {

	protected $feed_limit = 24;

	public function index()
	{
		$Events = new Events($this->api);
		$events = $Events->fetch(0, $this->feed_limit);

		$cell_template = $this->getHandlebarsTemplate('events/event-cell.hbs');
		$events_html = $this->renderHandlebars($cell_template, ['events' => $events]);

		$this->render([
			'js_options' => [
				'limit' => $this->feed_limit,
				'offset' => count($events)
			],
			'page_options' => [
				'cell_template' => $cell_template,
				'events_html' => $events_html
			]
		]);
	}

	/*
	 * Fetch more events
	 */
	public function fetch()
	{
		$page = !empty($_GET['offset']) ? $_GET['offset'] : 0;
		$limit = !empty($_GET['limit']) ? $_GET['limit'] : $this->feed_limit;
		$offset = $page * $limit;

		$Events = new Events($this->api);
		$events = $Events->fetch($offset, $limit);

		$this->renderJson([
			'status' => 0,
			'events' => $events,
			'count' => count($events)
		]);
	}

	/*
	 * Show deals for that event
	 */
	public function view($id)
	{
		$Events = new Events($this->api);
		$deals = $Events->fetchDeals($id);

		$cell_template = $this->getHandlebarsTemplate('deals/deal-cell.hbs');
		$deals_html = $this->renderHandlebars($cell_template, ['deals' => $deals]);

		$this->render('deals/index.html', [
			'js_options' => [
				'fetchUrl' => '/events/'. $id . '/deals',
				'limit' => $this->feed_limit,
			],
			'page_options' => [
				'cell_template' => $cell_template,
				'deals_html' => $deals_html
			]
		]);
	}

	/*
	 * Fetch more deals for event (based on daily_deal_id)
	 */
	public function deals($id)
	{
		$offset = !empty($_GET['offset']) ? $_GET['offset'] : 0;
		$limit = !empty($_GET['limit']) ? $_GET['limit'] : $this->feed_limit;

		$Events = new Events($this->api);
		$deals = $Events->fetchDeals($id, $offset, $limit);

		$this->renderJson([
			'status' => 0,
			'deals' => $deals,
			'count' => count($deals)
		]);
	}
}