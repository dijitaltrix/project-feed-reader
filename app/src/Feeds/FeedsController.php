<?php

namespace Feeds;

use Psr\Container\ContainerInterface;


class FeedsController
{
	/**
	 * Holds an instance of Slims container
	 * @var array
	 */
	private $container;


	/**
	 * Constructor receives slim container
	 *
	 * @param ContainerInterface $container 
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
	public function create($request, $response, $args=[])
	{
		die("create");
	}

	public function edit($request, $response, $args=[])
	{
		die("edit");
	}

	public function index($request, $response, $args=[])
	{
		die("index");
	}

	public function delete($request, $response, $args=[])
	{
		die("delete");
	}

	public function update($request, $response, $args=[])
	{
		die("update");
	}

	public function view($request, $response, $args=[])
	{
		die("view");
	}

}
