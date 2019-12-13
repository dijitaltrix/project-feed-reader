<?php

namespace Feeds;

class Feed extends \App\AbstractEntity
{
	/**
	 * Pseudo field
	 * Fetches the feed description from the remote url
	 * TODO an upgrade here would be to cache the description, using an updated_at timestamp
	 * 		in the database to judge when to refresh the data
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		return 'muppet';
	}
	/**
	 * Pseudo field
	 * Fetches the feed items from the remote url
	 *
	 * @return array
	 */
	public function getItems() : array
	{
		return [];
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
}
