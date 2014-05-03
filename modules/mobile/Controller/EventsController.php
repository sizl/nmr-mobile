<?php


namespace Nmr\Mobile\Controller;
use Nmr\Events;

class EventsController extends \Nmr\Application\Controller {

	public function index()
	{
		$this->route('get', function() {

			$limit = 2;

			$Events = new Events();
			$events = $Events->fetch(0, $limit);

			$cell_template = $this->getHandlebarsTemplate('events/event-cell.hbs');
			$events_html = $this->renderHandlebars($cell_template, ['events' => $events]);

			$this->render([
				'js_options' => ['limit' => $limit],
				'page_options' => [
					'cell_template' => $cell_template,
					'events_html' => $events_html
				]
			]);
		});
	}

	public function fetch()
	{
		$this->route('get', '/:limit/:offset', function($limit, $offset) {
			$Events = new Events();
			$deals = $Events->fetch($limit, $offset);
			$this->renderJson(['status' => 0, 'events' => $deals]);
		});
	}

	public function find()
	{
		$this->route('get', '/:deal_id', function($deal_id) {
			$Deals = new Deals();
			$deal = $Deals->find($deal_id);
			$this->renderJson(['status' => 0, 'deal' => $deal]);
		});
	}
}