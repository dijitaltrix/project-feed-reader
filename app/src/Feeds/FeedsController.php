<?php

namespace Feeds;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class FeedsController
{
    /**
     * Holds an instance of Slims container
     * @var array
     */
    private $app;
    /**
     * Shortcut to container log class
     * @var object
     */
    private $log;
    /**
     * Shortcut to container view class
     * @var object
     */
    private $view;


    /**
     * Constructor receives slim container
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->app = $container;	// I prefer to call it app, short and sweet
		$this->mapper = new FeedMapper($container->get('db'));
        $this->log = $container->get('log');
        $this->view = $container->get('view');
    }
    /**
     * Shows the 'create feed' view
     *
     * @param string $request
     * @param string $response
     * @param array $args
     * @return response
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        // grab previous input data
        $feed = $this->mapper->new();
        // grab alerts from session
        $alerts = [];

        return $this->view->render($response, '@feeds/create.twig', [
            'alerts' => $alerts,
            'feed' => $feed,
        ]);
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        die("edit");
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
		// fetch feeds, optionally filtering by name
		$where = [];
		$url_query = $request->getQueryParams();
		if (isset($url_query['name'])) {
			// use 'filtered' input in where clause
			$where = ["name" => $this->app->filter->string($name)];
		}
		$feeds = $this->mapper->fetch($where);
        // grab alerts from session (may have been deleted)
        $alerts = [];
        return $this->view->render($response, '@feeds/index.twig', [
            'alerts' => $alerts,
            'feeds' => $feeds,
        ]);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        die("delete");
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        die("update");
    }

    public function view(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        // find entity data, 'id' is filtered by the router
        $feed = $this->mapper->find( (int) $args['id']);
		$feed->attachReader($this->app->get('feed_reader'));

        return $this->view->render($response, '@feeds/view.twig', [
            'feed' => $feed,
        ]);

    }
}
