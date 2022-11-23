<?php

namespace App\Tests\Controllers\Front;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerCacheTest extends WebTestCase
{
    public function testCache(): void
    {
        $client = self::createClient();
        $client->enableProfiler();
        $this->assertTrue(true);

        $client->request('GET', '/video-list/category/movies,4/3');

        $this->assertGreaterThan(
            4,
            $client->getProfile()->getCollector('db')->getQueryCount()
        );

        $client->enableProfiler();
        $client->request('GET', '/video-list/category/movies,4/3');

        $this->assertEquals(
            1,
            $client->getProfile()->getCollector('db')->getQueryCount()
        );
    }
}
