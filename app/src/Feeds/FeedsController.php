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
        $this->validator = new FeedValidator($container->get('filter'));
    }
    /**
     * Shows the index 'browse feeds' view
     * this route has an optional filter to filter the returned items
     *
     * @param string $request
     * @param string $response
     * @param array $args
     * @return response
     */
    public function getIndex(ServerRequestInterface $request, ResponseInterface $response, $args=[])
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
        $alerts = $this->app->session->get('alerts', []);
        return $this->view->render($response, '@feeds/index.twig', [
            'alerts' => $alerts,
            'feeds' => $feeds,
        ]);
    }
    /**
     * Shows the 'create feed' view
     *
     * @param string $request
     * @param string $response
     * @param array $args
     * @return response
     */
    public function getCreate(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        // grab any available previous input data from session
        $feed = $this->mapper->new($this->app->session->get('input', []));
        // grab alerts and errors from session
        $alerts = $this->app->session->get('alerts', []);
        $errors = $this->app->session->get('errors', []);

        // forget keys as our session plugin does not do that
        $this->app->session->set('alerts', []);
        $this->app->session->set('errors', []);
        $this->app->session->set('input', []);

        return $this->view->render($response, '@feeds/create.twig', [
            'alerts' => $alerts,
            'errors' => $errors,
            'feed' => $feed,
        ]);
    }
    
    public function postInsert(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        $data = $request->getParsedBody();

        if ($this->validator->isValid($data)) {
            // pass new entity (filled and filtered) to mapper insert which returns entity
            $feed = $this->mapper->insert($this->mapper->new($data));
            // return success response, a redirect to view the new feed
            return $response
                ->withStatus(302)
                ->withHeader('Location', sprintf('/feeds/%d', $feed->id));
        }

        $entity = $this->mapper->new($data);
        // set errors, alerts and old data into session
        $this->app->session->set('alerts', [
            'warning' => 'Please correct the errors shown below'
        ]);
        $this->app->session->set('errors', $this->validator->getErrors());
        $this->app->session->set('input', $data);
        // return error response, redirect back to create form
        return $response
            ->withStatus(302)
            ->withHeader('Location', '/feeds/create');
    }

    public function getEdit(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        // grab any available previous input data from session
        $previous = $this->app->session->get('input', []);
        if (! empty($previous)) {
            $feed = $this->mapper->new($previous);
        } else {
            $feed = $this->mapper->find($args['id']);
        }
        // grab alerts and errors from session
        $alerts = $this->app->session->get('alerts', []);
        $errors = $this->app->session->get('errors', []);

        // forget keys as our session plugin does not do that
        $this->app->session->set('alerts', []);
        $this->app->session->set('errors', []);
        $this->app->session->set('input', []);

        return $this->view->render($response, '@feeds/edit.twig', [
            'alerts' => $alerts,
            'errors' => $errors,
            'feed' => $feed,
        ]);
    }

    public function getDelete(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        // fetch the item to be delete from storage
        $feed = $this->mapper->find($args['id']);
        // grab alerts and errors from session
        $alerts = $this->app->session->get('alerts', []);
        $errors = $this->app->session->get('errors', []);

        // forget keys as our session plugin does not do that
        $this->app->session->set('alerts', []);
        $this->app->session->set('errors', []);

        return $this->view->render($response, '@feeds/delete.twig', [
            'alerts' => $alerts,
            'errors' => $errors,
            'feed' => $feed,
        ]);
    }

    public function postDelete(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        // basic check to compare the hidden form body id with the url
        $feed = $this->mapper->find($args['id']);
        $data = $request->getParsedBody();
        
        if ($this->validator->isOkToDelete($data, $feed)) {
            $this->mapper->delete($feed);
			//NEXT Save in session to provide undo feature, would really require a POST to be RESTful
			$this->app->session->set("undo", $feed->getData());

            $this->app->session->set('alerts', [
                // 'success' => sprintf("The feed '%s' has been deleted, click to <a href=\"/feeds/restore\">undo</a>", $feed->name),
                'success' => sprintf("The feed '%s' has been deleted", $feed->name),
            ]);
            // return success response, a redirect to view the new feed
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/feeds');
        }

        // set errors, alerts and old data into session
        $this->app->session->set('alerts', [
            'warning' => 'Sorry the feed cannot be deleted'
        ]);
        $this->app->session->set('errors', $this->validator->getErrors());
        $this->app->session->set('input', $feed->getData());

        // return error response, redirect back to create form
        return $response
            ->withStatus(302)
            ->withHeader('Location', sprintf('/feeds/%d/edit', $feed->id));
    }

    public function postUpdate(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        $data = $request->getParsedBody();

        if ($this->validator->isValid($data)) {
            $feed = $this->mapper->find($args['id']);
            $result = $this->mapper->update($feed);
            // return success response, a redirect to view the new feed
            return $response
                ->withStatus(302)
                ->withHeader('Location', sprintf('/feeds/%d', $feed->id));
        }

        $feed = $this->mapper->new($data);
        // set errors, alerts and old data into session
        $this->app->session->set('alerts', [
            'warning' => 'Please correct the errors shown below'
        ]);
        $this->app->session->set('errors', $this->validator->getErrors());
        $this->app->session->set('input', $data);

        // return error response, redirect back to create form
        return $response
            ->withStatus(302)
            ->withHeader('Location', sprintf('/feeds/%d/edit', $feed->id));
    }

    public function getView(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        // find entity data, 'id' is filtered by the router
        $feed = $this->mapper->find((int) $args['id']);
        $feed->attachReader($this->app->get('feed_reader'));

        return $this->view->render($response, '@feeds/view.twig', [
            'feed' => $feed,
        ]);
    }

	public function getRestore(ServerRequestInterface $request, ResponseInterface $response, $args=[])
	{
		// check for undo key in session, if it passed validation then insert it
	}
}
