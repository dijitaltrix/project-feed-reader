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
        $this->log = $container['log'];
        $this->view = $container['view'];
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
        return $this->view->render($response, '@feeds/create.twig', [

        ]);
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        die("edit");
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        die("index");
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
        die("view");
    }
}
