<?php
namespace Tests;

class MockFeedMapper extends \Feeds\FeedMapper
{
    public function findBy($key, $value) : array
    {
        // this should react to data in FeedValidator test
        if ($key == 'url' && $value == 'http://localhost:8000/feed.xml') {
            return [
                [
                    'name' => 'An existing feed',
                    'url' => $value,
                ]
            ];
        }
        return [];
    }
}
