<?php


use Phinx\Seed\AbstractSeed;

class FeedsSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'name' => 'BBC',
                'url' => 'http://newsrss.bbc.co.uk/rss/newsonline_uk_edition/front_page/rss.xml',
            ],
            [
                'name' => 'PHP',
                'url' => 'http://www.php.net/news.rss',
            ],
            [
                'name' => 'Slashdot',
                'url' => 'http://slashdot.org/rss/slashdot.rss',
            ]
        ];

        $table = $this->table('feeds');
        $table->insert($data)
              ->save();
    }
}
