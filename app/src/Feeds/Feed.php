<?php

namespace Feeds;

class Feed extends \App\AbstractEntity
{
	/**
	 * Holds the feed reader class
	 * @var object
	 */
	private $reader;
	/**
	 * Holds the 'remote' description of the feed, fetched on demand
	 * @var object
	 */
	private $description;
	/**
	 * Holds the 'remote' list of feed items, fetched on demand
	 * @var array
	 */
	private $items;


	/**
	 * Attach the feed reader class
	 * NB: not named setReader as it will interfere with magic methods in AbstractEntity
	 *
	 * @param object $reader 
	 */
	public function attachReader($reader)
	{
		$this->reader = $reader;
	}
	/**
	 * Pseudo field
	 * Fetches the feed description from the remote url, once fetched it is saved in memory
	 * TODO an upgrade here would be to cache the description, using an updated_at timestamp
	 * 		in the database to judge when to refresh the data
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		if (empty($this->description)) {
			$this->fetchRemote();
		}

		return (string) $this->description;

	}
	/**
	 * Pseudo field
	 * Fetches the feed items from the remote url, once fetched they are saved in memory
	 *
	 * @return array
	 */
	public function getItems() : array
	{
		if (empty($this->items)) {
			$this->fetchRemote();
		}

		return (array) $this->items;

	}
	
	public function setId($int)
	{
		$this->data['id'] = (int) $int;
	}
	public function setName($str)
	{
		$this->data['name'] = $str;
	}
	public function setUrl($str)
	{
		$this->data['url'] = $str;
	}
	
	public function getId() : int
	{
		if ( ! isset($this->data['id'])) {
			throw new Exception("Cannot return unset property: 'id'");
		}
		
		return (int) $this->data['id'];

	}
	public function getName() : string
	{
		if ( ! isset($this->data['name'])) {
			throw new Exception("Cannot return unset property: 'name'");
		}
		
		return (string) $this->data['name'];

	}
	public function getUrl() : string
	{
		if ( ! isset($this->data['url'])) {
			throw new Exception("Cannot return unset property: 'url'");
		}
		
		return (string) $this->data['url'];

	}
	
	private function fetchRemote()
	{
		try {
			// see docs here http://simplepie.org/wiki/setup/sample_page
			$this->reader->set_feed_url($this->url);
			$this->reader->init();
			$this->reader->handle_content_type();

			// set pseudo fields, description and items
			$this->description = $this->reader->get_description();
			// just grab what we need for the view, get_items() returns lots of (useful?) cruft
			foreach ($this->reader->get_items() as $item)
			{
				$this->items[] = (object) [
					'date' => $item->get_date('jS M Y \a\t g:ia'),
					'author' => $item->get_author(),
					'title' => $item->get_title(),
					'description' => strip_tags($item->get_description()),
					'content' => strip_tags($item->get_content()),
					'link' => $item->get_permalink(),
				];
			}
			
		} catch (Exception $e) {
			
			// log error 
			throw $e;
			
		}
	}
}
