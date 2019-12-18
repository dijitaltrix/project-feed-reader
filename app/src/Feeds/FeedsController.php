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
        $this->mapper = new FeedMapper($container->get('db'), $container->get('feed_reader'));
        $this->log = $container->get('log');
        $this->view = $container->get('view');
        $this->validator = new FeedValidator($container->get('filter'), $this->mapper);
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
        $feeds = [];
        $where = [];
        $url_query = $request->getQueryParams();
        $query = null;
        if (isset($url_query['query'])) {
            // use 'filtered' input in where clause
            $query = $this->app->filter->alphanum($url_query['query']);
            if (! empty($query)) {
                $where = ["name" => "%$query%"];
                $feeds = $this->mapper->fetch($where);
            }
        }
        // grab alerts from session (may have been deleted)
        $alerts = $this->app->session->get('alerts', []);
        
        // forget keys as our session plugin does not do that
        $this->app->session->set('alerts', []);
        $this->app->session->set('errors', []);
        $this->app->session->set('input', []);
        
        return $this->view->render($response, '@feeds/index.twig', [
            'alerts' => $alerts,
            'nav' => $this->mapper->fetchNavList(),
            'feeds' => $feeds,
            'query' => $query,
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
            'nav' => $this->mapper->fetchNavList(),
            'feed' => $feed,
        ]);
    }
    
    public function postInsert(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        $data = $request->getParsedBody();

        if ($this->validator->isInsertable($data)) {
            // pass new entity (filled and filtered) to mapper insert which returns entity
            $feed = $this->mapper->new($data);
            // fetch a feed name if not supplied by the user
            if (empty($feed->name)) {
                $feed->name = $feed->fetchName();
            }
            // insert feed into storage
            $this->mapper->insert($feed);
            // set alert to show the user
            $this->app->session->set('alerts', [
                'success' => sprintf("Created the '%s' feed", $feed->name),
            ]);

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
            'nav' => $this->mapper->fetchNavList(),
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
            'nav' => $this->mapper->fetchNavList(),
            'feed' => $feed,
        ]);
    }

    public function postDelete(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        // basic check to compare the hidden form body id with the url
        $feed = $this->mapper->find($args['id']);
        $data = $request->getParsedBody();
        
        if ($this->validator->isDeletable($data, $feed)) {
            $this->mapper->delete($feed);
            //NEXT Save in session to provide undo feature, would really require a POST to be RESTful
            $this->app->session->set("undo", $feed->getData());

            $this->app->session->set('alerts', [
                // 'success' => sprintf("The feed '%s' has been deleted, click to <a href=\"/feeds/restore\">undo</a>", $feed->name),
                'success' => sprintf("The '%s' feed has been deleted", $feed->name),
            ]);
            
            if ($request->isXhr()) {
                // return a JSON success response to be handled in javascript
                return $response
                    ->withStatus(200)
                    ->withJson([
                        'status' => 200,
                        'redirect' => '/feeds',
                    ]);
            } else {
                // return success response, a redirect to view the new feed
                return $response
                    ->withStatus(302)
                    ->withHeader('Location', '/feeds');
            }
        }

        // set errors, alerts and old data into session
        $this->app->session->set('alerts', [
            'warning' => sprintf("Sorry the '%s' feed cannot be deleted", $feed->name)
        ]);
        $this->app->session->set('errors', $this->validator->getErrors());
        $this->app->session->set('input', $feed->getData());

        if ($request->isXhr()) {
            // return a JSON error response to be handled in javascript
            return $response
                ->withStatus(400)
                ->withJson([
                    'status' => 400,
                    'redirect' => sprintf('/feeds/%d/edit', $feed->id),
                ]);
        } else {
            // return success response, redirect back to create form
            return $response
                ->withStatus(302)
                ->withHeader('Location', sprintf('/feeds/%d/edit', $feed->id));
        }
    }

    public function postUpdate(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        $data = $request->getParsedBody();

        if ($this->validator->isUpdateable($data)) {
            $feed = $this->mapper->find($args['id']);
            $feed->setData($data);
            // disallow overriding of the id field (in the absence of any sanity chacking in the Entity)
            $feed->id = $args['id'];
            $result = $this->mapper->update($feed);
            $this->app->session->set('alerts', [
                'success' => sprintf("Updated the '%s' feed", $feed->name),
            ]);
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
        // grab any leftover alerts from session
        $alerts = $this->app->session->get('alerts', []);

        // forget keys as our session plugin does not do that
        $this->app->session->set('alerts', []);
        $this->app->session->set('errors', []);
        $this->app->session->set('input', []);

        return $this->view->render($response, '@feeds/view.twig', [
            'alerts' => $alerts,
            'nav' => $this->mapper->fetchNavList(),
            'feed' => $feed,
        ]);
    }

    public function getRestore(ServerRequestInterface $request, ResponseInterface $response, $args=[])
    {
        // check for undo key in session, if it passed validation then insert it
    }
}
