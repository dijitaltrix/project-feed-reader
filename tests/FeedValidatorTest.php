<?php
use PHPUnit\Framework\TestCase;

class FeedValidatorTest extends TestCase
{
    public function setUp()
    {
        // build validator with mock classes as necessary
        $this->validator = $this->buildFeedValidator();
    }
    /**
     * @dataProvider invalidInsertDataProvider
     */
    public function testInsertInvalidData($name, $url)
    {
        $validator = $this->buildFeedValidator();
        
        $result = $this->validator->isInsertable([
            'name' => $name,
            'url' => $url,
        ]);
        
        $this->assertFalse($result);
    }
    /**
     * @dataProvider validInsertDataProvider
     */
    public function testInsertValidData($name, $url)
    {
        $validator = $this->buildFeedValidator();
        
        $result = $this->validator->isInsertable([
            'name' => $name,
            'url' => $url,
        ]);
        
        $this->assertTrue($result);
    }
    /**
     * returns a select of invalid Feed data
     *
     * @return array
     */
    public function invalidInsertDataProvider()
    {
        return [
            [
                '', '',
            ],
            // require url
            [
                'Feed name', '',
            ],
            // invalid chars in name
            [
                '#!?`do bad things`', '',
            ],
            [
                str_repeat('blah ', ceil(256/5)), '',
            ],
            // invalid url
            [
                'Feed name', 'an invalid url',
            ],
            // invalid url
            [
                'Feed name', 'http://.com',
            ],
            // feed does not exist at this location
            [
                'Feed name', 'http://localhost:8000/not_found',
            ],
            // feed exists - see \Tests\MockFeedMapper::findBy
            [
                'Existing feed', 'http://localhost:8000/feed.xml',
            ],
            
            // more data or use Faker provider
            
        ];
    }
    /**
     * returns a select of valid Feed data
     *
     * @return array
     */
    public function validInsertDataProvider()
    {
        return [
            [
                'Ars Technica', 'http://feeds.arstechnica.com/arstechnica/index/',
            ],
            [
                'WIRED', 'http://feeds.wired.com/wired/index',
            ],
            
            // be careful how often we pull feeds, best to do from localhost
            
        ];
    }
    /**
     * Builds and returns the FeedValidator
     *
     * @return array
     */
    public function buildFeedValidator()
    {
        // should probably mock di
        $filter = new \App\Filter();
        $db = new \Tests\MockPDO();
        $reader = new \StdClass(); // no hard dependency here (for better/worse)
        $mapper = new \Tests\MockFeedMapper($db, $reader);
        $validator = new \Feeds\FeedValidator($filter, $mapper);

        return $validator;
    }
}
