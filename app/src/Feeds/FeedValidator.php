<?php
/**
 * This class handles the validation of the Feed object
 * It should really inherit from a base class with a common set of validation functions
 * Different methods can be used to handle different validation requirements (e.g. insert, update, delete)
 *
 * @author Ian Grindley
 */

namespace Feeds;

use App\Filter;
use Exception;


class FeedValidator
{
	/**
	 * Holds a keyed array of errors
	 * @var array
	 */
	private $errors;
	/**
	 * Holds the filter class
	 * @var object
	 */
	private $filter;


	/**
	 * Validator constructor requires a Filter, this should ideally
	 * be a requirement on an interface not a concrete class
	 *
	 * @param Filter $filter 
	 */
	public function __construct(Filter $filter)
	{
		$this->filter = $filter;
	}
	/**
	 * Check the Feed data is valid
	 *
	 * @param array $data 
	 * @return boolean
	 */
	public function isValid($data=[]) : bool
	{
		// check name input
		// not sure this will be required in the future as we can grab the name from the feed
		if ( ! isset($data['name']) OR empty($data['name'])) {
			$this->errors['name'][] = "You must name this feed";
		} else {
			if (strlen($data['name']) > 255) {
				$this->errors['name'][] = "Please shorten your feed name";
			}
			if ( ! $this->isValidAlphanum($data['name'])) {
				$this->errors['name'][] = "Please use only letters and numbers for your feed name";
			}
		}

		// check url input
		if ( ! isset($data['url']) OR empty($data['url'])) {
			$this->errors['url'][] = "You must provide a location for the feed";
		} else {
			// sanitise provided url
			$url = filter($data['url'], "url");
			// validate provided url
			if ( ! $this->isValidUrl($data['url'])) {
				$this->errors['url'][] = "Please check the url you have provided is correct";
			}
			// check url exists using curl - we get status 200
			$status = $this->getUrlStatus($url);
			if ($status !== 200)
			{
				$this->errors['url'][] = "Sorry, we cannot find a feed at that location, please check your url is correct";
			}
		}

		return (boolean) ! count($this->errors);

	}
	/**
	 * Check the Feed is okay to delete
	 *
	 * @param array $data 
	 * @param Feed $feed 
	 * @return boolean
	 */
	public function isOkToDelete($data, Feed $feed) : bool
	{
		if ( ! isset($data['id'])) {
			$this->errors['id'] = "No id supplied in delete form";
			return false;
		}
		if (empty($feed->id)) {
			$this->errors['id'] = "Cannot find the feed to delete";
			return false;
		}
		if ($data['id'] != $feed->id) {
			$this->errors['id'] = "Feed id mismatch";
			return false;
		}

		//TODO if this were important check in db for existing feed, match on other fields etc..

		return true;

	}

	public function getErrors() : array
	{
		if (is_array($this->errors) && ! empty($this->errors)) {
			return $this->errors;
		}

		return [];

	}
	/**
	 * Returns the HTTP status code generated at the $url
	 *
	 * @param string $url 
	 * @return integer
	 */
	private function getUrlStatus($url) : int
	{
		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_HEADER => true,
			CURLOPT_NOBODY => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 10,
		]);
		$response = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return (int) $status;

	}
	
	//
	//	These functions could easily be moved to a parent class
	//
	
	/**
	 * Validate a typical text string 
	 * accepts, letters a-z numbers 0-9 dashes - underscores _ dots . commas , and semi colons ;
	 *
	 * @param string $str 
	 * @return boolean
	 */
	private function isValidAlphanum($str) : bool
	{
		return (bool) ! preg_match('/[^a-z0-9\s\-\_\.\,\;]/i', $str);
		// not identifying false results which indicate an error
		// double negation which is not nice either.. to fix
	}
	/**
	 * Validate a url using the built in validator 
	 *
	 * @param string $str 
	 * @return boolean
	 */
	private function isValidUrl($str) : bool
	{
		return (bool) filter_var($str, FILTER_VALIDATE_URL);
	}
}